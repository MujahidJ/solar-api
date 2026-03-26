<?php

namespace App\Services;

use App\Models\ConditionEvent;
use App\Models\MaintenancePlan;
use App\Models\Reminder;

class ConditionReminderService
{
    public function handleEvent(ConditionEvent $event): ?Reminder
    {
        $installation = $event->installation()->with(['client', 'technicians'])->first();

        if (!$installation || !$installation->client) {
            return null;
        }

        $plan = MaintenancePlan::where('installation_id', $installation->id)
            ->where('active', true)
            ->whereIn('trigger_type', ['condition', 'hybrid'])
            ->where('condition_rule', $event->event_type)
            ->latest()
            ->first();

        if (!$plan) {
            return null;
        }

        $alreadyExists = Reminder::where('maintenance_plan_id', $plan->id)
            ->where('status', 'pending')
            ->where('trigger_source', 'condition')
            ->exists();

        if ($alreadyExists) {
            return null;
        }

        $assignedTech = $installation->technicians()->first();

        return Reminder::create([
            'installation_id' => $installation->id,
            'maintenance_plan_id' => $plan->id,
            'client_id' => $installation->client_id,
            'technician_id' => $assignedTech?->id,
            'message' => sprintf(
                'Condition-triggered maintenance for %s: %s (%s)',
                $installation->name,
                $plan->title,
                $event->event_type
            ),
            'due_date' => now()->toDateString(),
            'status' => 'pending',
            'trigger_source' => 'condition',
        ]);
    }
}