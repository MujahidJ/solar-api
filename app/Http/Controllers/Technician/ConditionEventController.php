<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\ConditionEvent;
use App\Models\Installation;
use App\Services\ConditionReminderService;
use Illuminate\Http\Request;

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
            'event_type' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $event = ConditionEvent::create([
            'installation_id' => $installation->id,
            'technician_id' => $user->id,
            'event_type' => $validated['event_type'],
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