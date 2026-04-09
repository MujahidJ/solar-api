<?php

namespace App\Http\Controllers\Technician;

use App\Enums\ConditionEventType;
use App\Http\Controllers\Controller;
use App\Models\ConditionEvent;
use App\Models\Installation;
use App\Services\ConditionReminderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConditionEventController extends Controller
{
    public function __construct(
        protected ConditionReminderService $conditionReminderService
    ) {}

    public function store(Request $request, Installation $installation)
    {
        $user = $request->user();

        $isAssigned = $user->assignedInstallations()
            ->where('installations.id', $installation->id)
            ->exists();

        if (!$isAssigned) {
            return response()->json([
                'message' => 'Forbidden: not assigned to this installation'
            ], 403);
        }

        $validated = $request->validate([
            'event_type' => ['required', Rule::enum(ConditionEventType::class)],
            'notes' => ['nullable', 'string'],
        ]);

        $event = ConditionEvent::create([
            'installation_id' => $installation->id,
            'technician_id' => $user->id,
            'event_type' => ConditionEventType::from($validated['event_type'])->value,
            'notes' => $validated['notes'] ?? null,
        ]);

        $reminder = $this->conditionReminderService->handleEvent($event);

        return response()->json([
            'message' => 'Condition event recorded successfully',
            'condition_event' => $event,
            'generated_reminder' => $reminder,
        ], 201);
    }
}