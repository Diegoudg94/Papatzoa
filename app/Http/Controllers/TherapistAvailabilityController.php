<?php

namespace App\Http\Controllers;

use App\Models\TherapistAvailabilityException;
use App\Models\TherapistAvailabilityRule;
use App\Models\TherapistAvailabilitySetting;
use App\Models\User;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TherapistAvailabilityController extends Controller
{
    public function index(AvailabilityService $availabilityService): View|RedirectResponse
    {
        $therapist = $this->currentTherapist();

        if ($therapist instanceof RedirectResponse) {
            return $therapist;
        }

        $settings = $this->settingsForView($therapist->id);
        $rules = TherapistAvailabilityRule::query()
            ->where('therapist_id', $therapist->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        $exceptions = TherapistAvailabilityException::query()
            ->where('therapist_id', $therapist->id)
            ->orderBy('exception_date')
            ->orderBy('start_time')
            ->get();
        $exceptionsByDate = $exceptions->groupBy(
            fn (TherapistAvailabilityException $exception): string => $exception->exception_date->format('Y-m-d')
        );

        $timezone = $settings->timezone ?: 'America/Mexico_City';
        $previewDays = max(1, min(14, (int) ($settings->maximum_booking_days ?: 60)));
        $previewFrom = Carbon::now($timezone)->startOfDay();
        $previewTo = $previewFrom->copy()->addDays($previewDays)->endOfDay();

        $previewSlots = $availabilityService->getAvailableSlotsFromPreloadedData(
            $therapist->id,
            $previewFrom,
            $previewTo,
            $settings,
            $rules,
            $exceptionsByDate
        );

        return view('terapeuta.disponibilidad', [
            'usuario' => $therapist,
            'settings' => $settings,
            'rules' => $rules,
            'exceptions' => $exceptions,
            'previewSlots' => $previewSlots,
            'dayNames' => $this->dayNames(),
            'timezones' => $this->timezones(),
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $therapist = $this->currentTherapist();

        if ($therapist instanceof RedirectResponse) {
            return $therapist;
        }

        $data = Validator::make($request->all(), [
            'timezone' => ['required', 'string', 'max:255'],
            'default_duration_minutes' => ['required', 'integer', 'between:15,240'],
            'buffer_before_minutes' => ['required', 'integer', 'between:0,120'],
            'buffer_after_minutes' => ['required', 'integer', 'between:0,120'],
            'minimum_notice_hours' => ['required', 'integer', 'between:0,720'],
            'maximum_booking_days' => ['required', 'integer', 'between:1,365'],
            'requires_confirmation' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ])->validateWithBag('settings');

        $data['requires_confirmation'] = $request->boolean('requires_confirmation');
        $data['is_active'] = $request->boolean('is_active');

        TherapistAvailabilitySetting::query()->updateOrCreate(
            ['therapist_id' => $therapist->id],
            $data
        );

        return back()->with('success', 'Configuración de disponibilidad guardada.');
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $therapist = $this->currentTherapist();

        if ($therapist instanceof RedirectResponse) {
            return $therapist;
        }

        $data = $this->validateRule($request, $therapist->id);
        $data['therapist_id'] = $therapist->id;
        $data['is_active'] = true;

        TherapistAvailabilityRule::create($data);

        return back()->with('success', 'Horario semanal agregado.');
    }

    public function updateRule(Request $request, int $id): RedirectResponse
    {
        $therapist = $this->currentTherapist();

        if ($therapist instanceof RedirectResponse) {
            return $therapist;
        }

        $rule = TherapistAvailabilityRule::query()
            ->where('id', $id)
            ->where('therapist_id', $therapist->id)
            ->firstOrFail();

        $data = $this->validateRule($request, $therapist->id, $rule->id);
        $data['is_active'] = $request->boolean('is_active', true);

        $rule->update($data);

        return back()->with('success', 'Horario semanal actualizado.');
    }

    public function destroyRule(int $id): RedirectResponse
    {
        $therapist = $this->currentTherapist();

        if ($therapist instanceof RedirectResponse) {
            return $therapist;
        }

        $rule = TherapistAvailabilityRule::query()
            ->where('id', $id)
            ->where('therapist_id', $therapist->id)
            ->firstOrFail();

        $rule->delete();

        return back()->with('success', 'Horario semanal eliminado.');
    }

    public function storeException(Request $request): RedirectResponse
    {
        $therapist = $this->currentTherapist();

        if ($therapist instanceof RedirectResponse) {
            return $therapist;
        }

        $data = Validator::make($request->all(), [
            'exception_date' => ['required', 'date'],
            'type' => ['required', 'in:blocked,available,vacation'],
            'start_time' => ['nullable', 'required_unless:type,vacation', 'date_format:H:i'],
            'end_time' => ['nullable', 'required_unless:type,vacation', 'date_format:H:i', 'after:start_time'],
            'reason' => ['nullable', 'string', 'max:255'],
        ])->validateWithBag('exceptions');

        if ($data['type'] === TherapistAvailabilityException::TYPE_VACATION) {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

        $data['therapist_id'] = $therapist->id;

        TherapistAvailabilityException::create($data);

        return back()->with('success', 'Excepción agregada.');
    }

    public function destroyException(int $id): RedirectResponse
    {
        $therapist = $this->currentTherapist();

        if ($therapist instanceof RedirectResponse) {
            return $therapist;
        }

        $exception = TherapistAvailabilityException::query()
            ->where('id', $id)
            ->where('therapist_id', $therapist->id)
            ->firstOrFail();

        $exception->delete();

        return back()->with('success', 'Excepción eliminada.');
    }

    public function preview(Request $request, AvailabilityService $availabilityService): array|RedirectResponse
    {
        $therapist = $this->currentTherapist();

        if ($therapist instanceof RedirectResponse) {
            return $therapist;
        }

        $settings = $this->settingsForView($therapist->id);
        $days = max(1, min((int) $request->integer('days', 14), 14));
        $timezone = $settings->timezone ?: 'America/Mexico_City';
        $previewFrom = Carbon::now($timezone)->startOfDay();
        $previewTo = $previewFrom->copy()->addDays($days)->endOfDay();

        return [
            'slots' => $availabilityService->getAvailableSlots(
                $therapist->id,
                $previewFrom,
                $previewTo
            ),
        ];
    }

    private function currentTherapist(): User|RedirectResponse
    {
        $therapistId = session('usuario_id');

        if (! $therapistId) {
            return redirect('/login');
        }

        $therapist = User::query()
            ->where('id', $therapistId)
            ->where('terapeuta', 1)
            ->first();

        abort_if(! $therapist, Response::HTTP_FORBIDDEN);

        return $therapist;
    }

    private function validateRule(Request $request, int $therapistId, ?int $ignoreRuleId = null): array
    {
        $validator = Validator::make($request->all(), [
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validator->after(function ($validator) use ($request, $therapistId, $ignoreRuleId) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $dayOfWeek = (int) $request->input('day_of_week');
            $startTime = $request->input('start_time');
            $endTime = $request->input('end_time');

            $exactDuplicate = TherapistAvailabilityRule::query()
                ->where('therapist_id', $therapistId)
                ->where('day_of_week', $dayOfWeek)
                ->where('start_time', $startTime)
                ->where('end_time', $endTime)
                ->when($ignoreRuleId, fn ($query) => $query->where('id', '!=', $ignoreRuleId))
                ->exists();

            if ($exactDuplicate) {
                $validator->errors()->add('start_time', 'Ya existe un intervalo idéntico para este día.');
                return;
            }

            $overlaps = TherapistAvailabilityRule::query()
                ->where('therapist_id', $therapistId)
                ->where('day_of_week', $dayOfWeek)
                ->when($ignoreRuleId, fn ($query) => $query->where('id', '!=', $ignoreRuleId))
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->exists();

            if ($overlaps) {
                $validator->errors()->add('start_time', 'Este intervalo se traslapa con otro horario del mismo día.');
            }
        });

        return $validator->validateWithBag('rules');
    }

    private function settingsForView(int $therapistId): TherapistAvailabilitySetting
    {
        return TherapistAvailabilitySetting::query()
            ->where('therapist_id', $therapistId)
            ->first()
            ?? new TherapistAvailabilitySetting([
                'therapist_id' => $therapistId,
                'timezone' => 'America/Mexico_City',
                'default_duration_minutes' => 60,
                'buffer_before_minutes' => 0,
                'buffer_after_minutes' => 0,
                'minimum_notice_hours' => 24,
                'maximum_booking_days' => 60,
                'requires_confirmation' => true,
                'is_active' => true,
            ]);
    }

    private function dayNames(): array
    {
        return [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
    }

    private function timezones(): array
    {
        return [
            'America/Mexico_City' => 'Ciudad de México',
            'America/Tijuana' => 'Tijuana',
            'America/Cancun' => 'Cancún',
            'America/Monterrey' => 'Monterrey',
            'America/New_York' => 'New York',
            'America/Los_Angeles' => 'Los Angeles',
            'Europe/Madrid' => 'Madrid',
            'UTC' => 'UTC',
        ];
    }
}
