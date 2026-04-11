<?php

namespace App\Http\Controllers\Technician;

use App\Enums\ConditionEventType;
use App\Http\Controllers\Controller;
use App\Models\ConditionEvent;
use App\Models\Installation;
use App\Services\EventActionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConditionEventController extends Controller
{
    public function __construct(
        protected EventActionService $eventActionService
    ) {}

    public function store(Request $request, Installation $installation)
    {
        $user = $request->user();

        if (! $this->isAssignedTechnician($user, $installation)) {
            return response()->json([
                'message' => 'Forbidden: not assigned to this installation',
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

        $result = $this->eventActionService->handle($event);

        return response()->json([
            'message' => 'Condition event recorded successfully',
            'condition_event' => $event->fresh(),
            'generated_reminder' => $result['reminder'],
            'notifications' => $result['notifications'],
        ], 201);
    }

    protected function isAssignedTechnician($user, Installation $installation): bool
    {
        return $user->assignedInstallations()
            ->where('installations.id', $installation->id)
            ->exists();
    }
}