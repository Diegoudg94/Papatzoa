<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\TherapistAvailabilitySetting;
use App\Models\TherapistAvailabilityRule;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientAppointmentController extends Controller
{
    private const BLOCKING_STATUSES = [
        'pendiente',
        'confirmada',
        'confirmado',
        'aceptada',
        'aceptado',
    ];

    public function index(): View|RedirectResponse
    {
        $patient = $this->currentPatient();

        if ($patient instanceof RedirectResponse) {
            return $patient;
        }

        $citas = DB::table('citas')
            ->where('paciente_id', $patient->id)
            ->orderByRaw('COALESCE(starts_at, fecha::timestamp) DESC')
            ->orderBy('hora', 'desc')
            ->get();

        $terapeuta = $patient->terapeuta_id
            ? User::query()
                ->where('id', $patient->terapeuta_id)
                ->where('terapeuta', 1)
                ->first()
            : null;

        $settings = $terapeuta
            ? $this->settingsFor($terapeuta->id)
            : $this->defaultSettings();

        $therapistName = $terapeuta
            ? (trim(($terapeuta->nombre ?? '') . ' ' . ($terapeuta->apellido ?? '')) ?: 'Terapeuta')
            : null;
        $therapistAvatar = $terapeuta ? $this->therapistAvatarUrl($terapeuta) : null;
        $availableModalities = $terapeuta ? $this->modalitiesForTherapist($terapeuta) : [];

        return view('citas', [
            'citas' => $citas,
            'usuario' => $patient,
            'terapeuta' => $terapeuta,
            'availabilityTimezone' => $settings['timezone'],
            'availabilityDurationMinutes' => $settings['default_duration_minutes'],
            'availabilityIsActive' => $settings['is_active'],
            'therapistHasRules' => $terapeuta ? $this->therapistHasActiveRules($terapeuta->id) : false,
            'therapistName' => $therapistName,
            'therapistAvatarUrl' => $therapistAvatar,
            'therapistAvailableModalities' => $availableModalities,
        ]);
    }

    public function availability(Request $request, AvailabilityService $availabilityService): JsonResponse
    {
        $patient = $this->currentPatientForJson();

        if (! $patient->terapeuta_id) {
            return response()->json([
                'message' => 'Necesitas vincularte con un terapeuta antes de solicitar una cita.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $therapist = User::query()
            ->where('id', $patient->terapeuta_id)
            ->where('terapeuta', 1)
            ->first();

        if (! $therapist) {
            return response()->json([
                'message' => 'No encontramos el terapeuta vinculado a tu cuenta.',
            ], Response::HTTP_NOT_FOUND);
        }

        $settings = $this->settingsFor($therapist->id);
        $timezone = $settings['timezone'];

        $data = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($data['from'], $timezone)->startOfDay();
        $to = Carbon::parse($data['to'], $timezone)->endOfDay();

        if ($from->diffInDays($to) > 31) {
            return response()->json([
                'message' => 'El rango máximo consultable es de 31 días.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $slots = $availabilityService->getAvailableSlots($therapist->id, $from, $to);
        $therapistName = trim(($therapist->nombre ?? '') . ' ' . ($therapist->apellido ?? '')) ?: 'Terapeuta';
        $hasRules = $this->therapistHasActiveRules($therapist->id);
        $message = null;

        if (! $settings['is_active'] || ! $hasRules) {
            $message = 'Tu terapeuta todavia no ha habilitado horarios para reservar.';
        } elseif (empty($slots)) {
            $message = 'No hay horarios disponibles en los proximos 14 dias.';
        }

        return response()->json([
            'timezone' => $timezone,
            'therapist' => [
                'id' => $therapist->id,
                'name' => $therapistName,
                'avatar_url' => $this->therapistAvatarUrl($therapist),
            ],
            'session_duration_minutes' => $settings['default_duration_minutes'],
            'available_modalities' => $this->modalitiesForTherapist($therapist),
            'availability_active' => $settings['is_active'] && $hasRules,
            'message' => $message,
            'slots' => array_map(static function (array $slot): array {
                return [
                    'start' => $slot['start'],
                    'end' => $slot['end'],
                    'date' => str($slot['start'])->substr(0, 10)->toString(),
                    'label' => $slot['label'],
                ];
            }, $slots),
        ]);
    }

    public function store(Request $request, AvailabilityService $availabilityService): RedirectResponse
    {
        $patient = $this->currentPatient();

        if ($patient instanceof RedirectResponse) {
            return $patient;
        }

        if (! $patient->terapeuta_id) {
            return redirect('/citas')
                ->with('error_cita', 'Primero debes vincularte con un terapeuta para solicitar una cita.');
        }

        $therapist = User::query()
            ->where('id', $patient->terapeuta_id)
            ->where('terapeuta', 1)
            ->first();

        if (! $therapist) {
            return redirect('/citas')
                ->with('error_cita', 'No encontramos el terapeuta vinculado a tu cuenta.');
        }

        $data = $request->validate([
            'start' => ['required', 'date_format:Y-m-d\TH:i:sP'],
            'end' => ['required', 'date_format:Y-m-d\TH:i:sP', 'after:start'],
            'motivo' => ['required', 'string', 'max:4000'],
            'modalidad' => ['nullable', 'string', 'max:100'],
        ]);

        $settings = $this->settingsFor($therapist->id);
        $timezone = $settings['timezone'];
        $start = Carbon::parse($data['start'])->setTimezone($timezone);
        $end = Carbon::parse($data['end'])->setTimezone($timezone);
        $now = Carbon::now($timezone);
        $duration = (int) round($start->diffInMinutes($end));
        $allowedModalities = $this->modalitiesForTherapist($therapist);
        $selectedModalidad = null;

        if (count($allowedModalities) === 1) {
            $selectedModalidad = $allowedModalities[0];
        } elseif (count($allowedModalities) > 1) {
            if (! ($data['modalidad'] ?? null) || ! in_array($data['modalidad'], $allowedModalities, true)) {
                return back()->withInput()->with('error_cita', 'Selecciona una modalidad valida para esta cita.');
            }

            $selectedModalidad = $data['modalidad'];
        } elseif (! empty($data['modalidad'])) {
            return back()->withInput()->with('error_cita', 'La modalidad seleccionada no es valida.');
        }

        if ($start->lessThanOrEqualTo($now)) {
            return back()->withInput()->with('error_cita', 'Selecciona un horario futuro.');
        }

        if ($start->lessThan($now->copy()->addHours($settings['minimum_notice_hours']))) {
            return back()->withInput()->with('error_cita', 'Este horario ya no cumple con la anticipación mínima.');
        }

        if ($end->greaterThan($now->copy()->addDays($settings['maximum_booking_days'])->endOfDay())) {
            return back()->withInput()->with('error_cita', 'Este horario está fuera del rango permitido para reservar.');
        }

        if ($duration !== (int) $settings['default_duration_minutes']) {
            return back()->withInput()->with('error_cita', 'La duración del horario seleccionado no es válida.');
        }

        if (! $this->generatedSlotExists($availabilityService, $therapist->id, $start, $end)) {
            return back()->withInput()->with('error_cita', 'Este horario ya no está disponible. Selecciona otro.');
        }

        if (! $availabilityService->isSlotAvailable($therapist->id, $start, $end)) {
            return back()->withInput()->with('error_cita', 'Este horario ya no está disponible. Selecciona otro.');
        }

        try {
            DB::transaction(function () use ($patient, $therapist, $settings, $start, $end, $duration, $data, $selectedModalidad) {
                $hasOverlap = Cita::query()
                    ->where('terapeuta_id', $therapist->id)
                    ->whereIn('estado', self::BLOCKING_STATUSES)
                    ->whereNotNull('starts_at')
                    ->whereNotNull('ends_at')
                    ->where('starts_at', '<', $end)
                    ->where('ends_at', '>', $start)
                    ->lockForUpdate()
                    ->exists();

                if ($hasOverlap) {
                    throw new SlotUnavailableException;
                }

                $estado = $settings['requires_confirmation'] ? 'pendiente' : 'confirmada';
                $now = Carbon::now($settings['timezone']);

                Cita::create([
                    'paciente_id' => $patient->id,
                    'terapeuta_id' => $therapist->id,
                    'fecha' => $start->toDateString(),
                    'hora' => $start->format('H:i:s'),
                    'motivo' => $data['motivo'],
                    'motivo_encrypted' => Crypt::encryptString($data['motivo']),
                    'estado' => $estado,
                    'comentario_terapeuta' => null,
                    'starts_at' => $start,
                    'ends_at' => $end,
                    'timezone' => $settings['timezone'],
                    'duration_minutes' => $duration,
                    'modalidad' => $selectedModalidad,
                    'requested_at' => $now,
                    'confirmed_at' => $estado === 'confirmada' ? $now : null,
                ]);
            });
        } catch (SlotUnavailableException) {
            return back()->withInput()->with('error_cita', 'Este horario ya no está disponible. Selecciona otro.');
        }

        return redirect('/citas')
            ->with('success_cita', 'Tu solicitud de cita fue enviada correctamente.');
    }

    private function currentPatient(): User|RedirectResponse
    {
        $patientId = session('usuario_id');

        if (! $patientId) {
            return redirect('/login');
        }

        $patient = User::query()
            ->where('id', $patientId)
            ->where('terapeuta', 0)
            ->first();

        abort_if(! $patient, Response::HTTP_FORBIDDEN);

        return $patient;
    }

    private function settingsFor(int $therapistId): array
    {
        $settings = TherapistAvailabilitySetting::query()
            ->where('therapist_id', $therapistId)
            ->first();

        return [
            'timezone' => $settings?->timezone ?: 'America/Mexico_City',
            'default_duration_minutes' => (int) ($settings?->default_duration_minutes ?: 60),
            'minimum_notice_hours' => (int) ($settings?->minimum_notice_hours ?: 24),
            'maximum_booking_days' => (int) ($settings?->maximum_booking_days ?: 60),
            'requires_confirmation' => $settings?->requires_confirmation ?? true,
            'is_active' => $settings?->is_active ?? true,
        ];
    }

    private function currentPatientForJson(): User
    {
        $patientId = session('usuario_id');

        if (! $patientId) {
            abort(Response::HTTP_UNAUTHORIZED, 'Debes iniciar sesion.');
        }

        $patient = User::query()
            ->where('id', $patientId)
            ->where('terapeuta', 0)
            ->first();

        abort_if(! $patient, Response::HTTP_FORBIDDEN);

        return $patient;
    }

    private function generatedSlotExists(AvailabilityService $availabilityService, int $therapistId, Carbon $start, Carbon $end): bool
    {
        foreach ($availabilityService->generateSlotsForDate($therapistId, $start->copy()) as $slot) {
            $slotStart = Carbon::parse($slot['start'])->getTimestamp();
            $slotEnd = Carbon::parse($slot['end'])->getTimestamp();

            if ($slotStart === $start->getTimestamp() && $slotEnd === $end->getTimestamp()) {
                return true;
            }
        }

        return false;
    }

    private function defaultSettings(): array
    {
        return [
            'timezone' => 'America/Mexico_City',
            'default_duration_minutes' => 60,
            'minimum_notice_hours' => 24,
            'maximum_booking_days' => 60,
            'requires_confirmation' => true,
            'is_active' => true,
        ];
    }

    private function therapistHasActiveRules(int $therapistId): bool
    {
        return TherapistAvailabilityRule::query()
            ->where('therapist_id', $therapistId)
            ->where('is_active', true)
            ->exists();
    }

    private function therapistAvatarUrl(User $therapist): ?string
    {
        if (! empty($therapist->profile_photo_path)) {
            return asset('storage/' . ltrim($therapist->profile_photo_path, '/'));
        }

        return ! empty($therapist->avatar_url) ? $therapist->avatar_url : null;
    }

    private function modalitiesForTherapist(User $therapist): array
    {
        $value = strtolower(trim((string) ($therapist->modalidad_atencion ?? '')));

        return match ($value) {
            'online' => ['online'],
            'presencial' => ['presencial'],
            'hibrida', 'híbrida' => ['online', 'presencial'],
            default => [],
        };
    }
}

class SlotUnavailableException extends \RuntimeException
{
}
