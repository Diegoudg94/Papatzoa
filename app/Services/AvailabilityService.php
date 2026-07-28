<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\TherapistAvailabilityException;
use App\Models\TherapistAvailabilityRule;
use App\Models\TherapistAvailabilitySetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AvailabilityService
{
    private const BLOCKING_STATUSES = [
        'pendiente',
        'confirmada',
        'confirmado',
        'aceptada',
        'aceptado',
    ];

    private const DEFAULT_TIMEZONE = 'America/Mexico_City';

    private const MAX_SLOTS_PER_DAY = 200;

    public function getAvailableSlots(int $therapistId, Carbon $from, Carbon $to): array
    {
        $settings = $this->resolveSettings($therapistId);

        return $this->getAvailableSlotsFromContext(
            $therapistId,
            $from,
            $to,
            $settings,
            $this->loadRulesByDay($therapistId),
            $this->loadExceptionsByDate($therapistId, $from, $to)
        );
    }

    public function getAvailableSlotsFromPreloadedData(
        int $therapistId,
        Carbon $from,
        Carbon $to,
        TherapistAvailabilitySetting $settings,
        Collection $rulesByDay,
        Collection $exceptionsByDate
    ): array {
        return $this->getAvailableSlotsFromContext(
            $therapistId,
            $from,
            $to,
            $settings,
            $rulesByDay,
            $exceptionsByDate
        );
    }

    private function getAvailableSlotsFromContext(
        int $therapistId,
        Carbon $from,
        Carbon $to,
        TherapistAvailabilitySetting $settings,
        Collection $rulesByDay,
        Collection $exceptionsByDate
    ): array {
        $settings = $this->normalizeSettings($settings);

        if (! $settings->is_active) {
            return [];
        }

        $timezone = $settings->timezone ?: self::DEFAULT_TIMEZONE;
        $previewDays = max(1, min(14, (int) $settings->maximum_booking_days));
        $now = Carbon::now($timezone);
        $minimumStart = $now->copy()->addHours((int) $settings->minimum_notice_hours);
        $maximumEnd = $now->copy()->addDays((int) $settings->maximum_booking_days)->endOfDay();

        $previewStart = $from->copy()->setTimezone($timezone)->startOfDay();
        $previewEnd = $previewStart->copy()->addDays($previewDays)->endOfDay();
        $requestedStart = $from->copy()->setTimezone($timezone);
        $requestedEnd = $to->copy()->setTimezone($timezone);

        $rangeStart = $previewStart->max($requestedStart)->max($minimumStart);
        $rangeEnd = $previewEnd->min($requestedEnd)->min($maximumEnd);

        if ($rangeStart->greaterThan($rangeEnd)) {
            return [];
        }

        $relevantExceptions = $exceptionsByDate->filter(
            fn (Collection $group, string $dateKey): bool => $dateKey >= $rangeStart->toDateString()
                && $dateKey <= $rangeEnd->toDateString()
        );
        $appointments = $this->loadAppointments($therapistId, $rangeStart, $rangeEnd);

        $slots = [];
        $date = $rangeStart->copy()->startOfDay();
        $lastDate = $rangeEnd->copy()->startOfDay();

        while ($date->lessThanOrEqualTo($lastDate)) {
            $dateSlots = $this->generateSlotsForDateFromData(
                $date,
                $settings,
                $rulesByDay->get($date->dayOfWeek, collect()),
                $relevantExceptions->get($date->toDateString(), collect()),
                $appointments
            );

            foreach ($dateSlots as $slot) {
                $slotStart = Carbon::parse($slot['start'])->setTimezone($timezone);
                $slotEnd = Carbon::parse($slot['end'])->setTimezone($timezone);

                if ($slotStart->greaterThanOrEqualTo($rangeStart) && $slotEnd->lessThanOrEqualTo($rangeEnd)) {
                    $slots[] = $slot;
                }
            }

            $date->addDay();
        }

        return $slots;
    }

    public function generateSlotsForDate(int $therapistId, Carbon $date): array
    {
        $settings = $this->normalizeSettings($this->resolveSettings($therapistId));

        if (! $settings->is_active) {
            return [];
        }

        $timezone = $settings->timezone ?: self::DEFAULT_TIMEZONE;
        $dateInTimezone = $date->copy()->setTimezone($timezone)->startOfDay();

        return $this->generateSlotsForDateFromData(
            $dateInTimezone,
            $settings,
            $this->loadRulesByDay($therapistId)->get($dateInTimezone->dayOfWeek, collect()),
            $this->loadExceptionsByDate($therapistId, $dateInTimezone, $dateInTimezone)->get($dateInTimezone->toDateString(), collect()),
            $this->loadAppointments($therapistId, $dateInTimezone->copy()->startOfDay(), $dateInTimezone->copy()->endOfDay())
        );
    }

    public function isSlotAvailable(int $therapistId, Carbon $start, Carbon $end, ?int $ignoreAppointmentId = null): bool
    {
        $settings = $this->resolveSettings($therapistId);
        $timezone = $settings->timezone ?: self::DEFAULT_TIMEZONE;
        $start = $start->copy()->setTimezone($timezone);
        $end = $end->copy()->setTimezone($timezone);

        if ($end->lessThanOrEqualTo($start)) {
            return false;
        }

        if (! $settings->is_active) {
            return false;
        }

        if (! $start->isSameDay($end->copy()->subSecond())) {
            return false;
        }

        $now = Carbon::now($timezone);
        $minimumStart = $now->copy()->addHours((int) $settings->minimum_notice_hours);
        $maximumEnd = $now->copy()->addDays((int) $settings->maximum_booking_days)->endOfDay();

        if ($start->lessThan($minimumStart) || $end->greaterThan($maximumEnd)) {
            return false;
        }

        $availableIntervals = $this->availabilityIntervalsForDate(
            $start->copy()->startOfDay(),
            $timezone,
            $this->loadRulesByDay($therapistId)->get($start->dayOfWeek, collect()),
            $this->loadExceptionsByDate($therapistId, $start->copy()->startOfDay(), $start->copy()->startOfDay())->get($start->toDateString(), collect())
        );

        if (! $this->isContainedInIntervals($availableIntervals, $start, $end)) {
            return false;
        }

        $requestedStart = $start->copy()->subMinutes(max(0, (int) $settings->buffer_before_minutes));
        $requestedEnd = $end->copy()->addMinutes(max(0, (int) $settings->buffer_after_minutes));

        $query = Cita::query()
            ->where('terapeuta_id', $therapistId)
            ->whereIn('estado', self::BLOCKING_STATUSES)
            ->whereNotNull('starts_at')
            ->whereNotNull('ends_at')
            ->where('starts_at', '<', $requestedEnd)
            ->where('ends_at', '>', $requestedStart);

        if ($ignoreAppointmentId !== null) {
            $query->whereKeyNot($ignoreAppointmentId);
        }

        return ! $query->exists();
    }

    private function generateSlotsForDateFromData(
        Carbon $date,
        TherapistAvailabilitySetting $settings,
        Collection $rulesForDay,
        Collection $exceptionsForDate,
        Collection $appointments
    ): array {
        if (! $settings->is_active) {
            return [];
        }

        $timezone = $settings->timezone ?: self::DEFAULT_TIMEZONE;
        $date = $date->copy()->setTimezone($timezone)->startOfDay();
        $now = Carbon::now($timezone);
        $minimumStart = $now->copy()->addHours((int) $settings->minimum_notice_hours);
        $maximumEnd = $now->copy()->addDays((int) $settings->maximum_booking_days)->endOfDay();

        if ($date->greaterThan($maximumEnd) || $date->copy()->endOfDay()->lessThan($minimumStart)) {
            return [];
        }

        $intervals = $this->availabilityIntervalsForDate($date, $timezone, $rulesForDay, $exceptionsForDate);

        if ($intervals->isEmpty()) {
            return [];
        }

        $duration = max(0, (int) $settings->default_duration_minutes);
        $bufferBefore = max(0, (int) $settings->buffer_before_minutes);
        $bufferAfter = max(0, (int) $settings->buffer_after_minutes);

        if ($duration <= 0) {
            return [];
        }

        $slots = [];

        foreach ($intervals as $interval) {
            $cursor = $interval['start']->copy();
            $intervalEnd = $interval['end']->copy();
            $generatedSlots = 0;

            while ($cursor->copy()->addMinutes($duration)->lessThanOrEqualTo($intervalEnd)) {
                if ($generatedSlots >= self::MAX_SLOTS_PER_DAY) {
                    break;
                }

                $slotStart = $cursor->copy();
                $slotEnd = $slotStart->copy()->addMinutes($duration);

                if (
                    $slotStart->greaterThanOrEqualTo($minimumStart)
                    && $slotEnd->lessThanOrEqualTo($maximumEnd)
                    && ! $this->hasAppointmentConflict($appointments, $slotStart, $slotEnd, $bufferBefore, $bufferAfter)
                ) {
                    $slots[] = [
                        'start' => $slotStart->toIso8601String(),
                        'end' => $slotEnd->toIso8601String(),
                        'label' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                    ];
                }

                $cursor = $slotEnd->copy()->addMinutes($bufferAfter);
                $generatedSlots++;
            }
        }

        return $slots;
    }

    private function hasAppointmentConflict(
        Collection $appointments,
        Carbon $slotStart,
        Carbon $slotEnd,
        int $bufferBefore,
        int $bufferAfter
    ): bool {
        $requestedStart = $slotStart->copy()->subMinutes($bufferBefore);
        $requestedEnd = $slotEnd->copy()->addMinutes($bufferAfter);

        return $appointments->contains(function (array $appointment) use ($requestedStart, $requestedEnd): bool {
            return $appointment['start']->lt($requestedEnd)
                && $appointment['end']->gt($requestedStart);
        });
    }

    private function isContainedInIntervals(Collection $intervals, Carbon $start, Carbon $end): bool
    {
        return $intervals->contains(
            fn (array $interval): bool => $start->greaterThanOrEqualTo($interval['start'])
                && $end->lessThanOrEqualTo($interval['end'])
        );
    }

    private function resolveSettings(int $therapistId): TherapistAvailabilitySetting
    {
        $settings = TherapistAvailabilitySetting::query()
            ->where('therapist_id', $therapistId)
            ->first();

        if ($settings) {
            return $this->normalizeSettings($settings);
        }

        return new TherapistAvailabilitySetting([
            'therapist_id' => $therapistId,
            'timezone' => self::DEFAULT_TIMEZONE,
            'default_duration_minutes' => 60,
            'buffer_before_minutes' => 0,
            'buffer_after_minutes' => 0,
            'minimum_notice_hours' => 24,
            'maximum_booking_days' => 60,
            'requires_confirmation' => true,
            'is_active' => true,
        ]);
    }

    private function normalizeSettings(TherapistAvailabilitySetting $settings): TherapistAvailabilitySetting
    {
        $settings->timezone = $settings->timezone ?: self::DEFAULT_TIMEZONE;
        $settings->default_duration_minutes = max(0, (int) ($settings->default_duration_minutes ?: 60));
        $settings->buffer_before_minutes = max(0, (int) ($settings->buffer_before_minutes ?: 0));
        $settings->buffer_after_minutes = max(0, (int) ($settings->buffer_after_minutes ?: 0));
        $settings->minimum_notice_hours = max(0, (int) ($settings->minimum_notice_hours ?: 24));
        $settings->maximum_booking_days = max(1, (int) ($settings->maximum_booking_days ?: 60));
        $settings->requires_confirmation = $settings->requires_confirmation ?? true;
        $settings->is_active = $settings->is_active ?? true;

        return $settings;
    }

    private function loadRulesByDay(int $therapistId): Collection
    {
        return TherapistAvailabilityRule::query()
            ->where('therapist_id', $therapistId)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
    }

    private function loadExceptionsByDate(int $therapistId, Carbon $from, Carbon $to): Collection
    {
        return TherapistAvailabilityException::query()
            ->where('therapist_id', $therapistId)
            ->whereBetween('exception_date', [
                $from->copy()->toDateString(),
                $to->copy()->toDateString(),
            ])
            ->orderBy('exception_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (TherapistAvailabilityException $exception): string => $exception->exception_date->format('Y-m-d'));
    }

    private function loadAppointments(int $therapistId, Carbon $from, Carbon $to): Collection
    {
        return DB::table('citas')
            ->where('terapeuta_id', $therapistId)
            ->whereIn('estado', self::BLOCKING_STATUSES)
            ->whereNotNull('starts_at')
            ->whereNotNull('ends_at')
            ->where('starts_at', '<', $to)
            ->where('ends_at', '>', $from)
            ->get()
            ->map(function (object $appointment): array {
                return [
                    'id' => $appointment->id,
                    'start' => Carbon::parse($appointment->starts_at),
                    'end' => Carbon::parse($appointment->ends_at),
                ];
            })
            ->values();
    }

    private function availabilityIntervalsForDate(
        Carbon $date,
        string $timezone,
        Collection $rulesForDay,
        Collection $exceptionsForDate
    ): Collection {
        $intervals = $rulesForDay
            ->map(fn (TherapistAvailabilityRule $rule): array => [
                'start' => $this->dateTimeFromTime($date, $rule->start_time, $timezone),
                'end' => $this->dateTimeFromTime($date, $rule->end_time, $timezone),
            ])
            ->filter(fn (array $interval): bool => $interval['end']->greaterThan($interval['start']))
            ->values();

        if ($exceptionsForDate->contains('type', TherapistAvailabilityException::TYPE_VACATION)) {
            return collect();
        }

        foreach ($exceptionsForDate as $exception) {
            if ($exception->type === TherapistAvailabilityException::TYPE_AVAILABLE) {
                if ($exception->start_time === null || $exception->end_time === null) {
                    continue;
                }

                $intervals->push([
                    'start' => $this->dateTimeFromTime($date, $exception->start_time, $timezone),
                    'end' => $this->dateTimeFromTime($date, $exception->end_time, $timezone),
                ]);
            }
        }

        $intervals = $this->mergeIntervals($intervals);

        foreach ($exceptionsForDate as $exception) {
            if ($exception->type !== TherapistAvailabilityException::TYPE_BLOCKED) {
                continue;
            }

            $blockStart = $exception->start_time === null
                ? $date->copy()->startOfDay()
                : $this->dateTimeFromTime($date, $exception->start_time, $timezone);
            $blockEnd = $exception->end_time === null
                ? $date->copy()->endOfDay()
                : $this->dateTimeFromTime($date, $exception->end_time, $timezone);

            $intervals = $this->subtractInterval($intervals, $blockStart, $blockEnd);
        }

        return $this->mergeIntervals($intervals);
    }

    private function dateTimeFromTime(Carbon $date, string $time, string $timezone): Carbon
    {
        $parts = explode(':', $time);

        return $date->copy()
            ->setTimezone($timezone)
            ->setTime((int) $parts[0], (int) ($parts[1] ?? 0), (int) ($parts[2] ?? 0));
    }

    private function mergeIntervals(Collection $intervals): Collection
    {
        $sorted = $intervals
            ->filter(fn (array $interval): bool => $interval['end']->greaterThan($interval['start']))
            ->sortBy(fn (array $interval): int => $interval['start']->getTimestamp())
            ->values();

        $merged = collect();

        foreach ($sorted as $interval) {
            if ($merged->isEmpty()) {
                $merged->push($interval);
                continue;
            }

            $last = $merged->last();

            if ($interval['start']->lessThanOrEqualTo($last['end'])) {
                $last['end'] = $interval['end']->greaterThan($last['end']) ? $interval['end'] : $last['end'];
                $merged->pop();
                $merged->push($last);
                continue;
            }

            $merged->push($interval);
        }

        return $merged;
    }

    private function subtractInterval(Collection $intervals, Carbon $blockStart, Carbon $blockEnd): Collection
    {
        if ($blockEnd->lessThanOrEqualTo($blockStart)) {
            return $intervals;
        }

        $result = collect();

        foreach ($intervals as $interval) {
            if ($interval['end']->lessThanOrEqualTo($blockStart) || $interval['start']->greaterThanOrEqualTo($blockEnd)) {
                $result->push($interval);
                continue;
            }

            if ($interval['start']->lessThan($blockStart)) {
                $result->push([
                    'start' => $interval['start'],
                    'end' => $blockStart->copy(),
                ]);
            }

            if ($interval['end']->greaterThan($blockEnd)) {
                $result->push([
                    'start' => $blockEnd->copy(),
                    'end' => $interval['end'],
                ]);
            }
        }

        return $result->values();
    }
}
