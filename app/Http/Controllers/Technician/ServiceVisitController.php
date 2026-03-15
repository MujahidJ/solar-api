<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use App\Models\MaintenancePlan;
use App\Models\Reminder;
use App\Models\ServiceVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServiceVisitController extends Controller
{
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
            'serviced_on' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $visit = ServiceVisit::create([
            'installation_id' => $installation->id,
            'technician_id' => $user->id,
            'serviced_on' => $validated['serviced_on'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $plan = MaintenancePlan::where('installation_id', $installation->id)
            ->where('active', true)
            ->latest()
            ->first();

        $updatedPlan = null;
        $completedReminder = null;

        if ($plan && $plan->interval_days) {
            $nextDueDate = Carbon::parse($validated['serviced_on'])
                ->addDays($plan->interval_days)
                ->toDateString();

            $plan->next_due_date = $nextDueDate;
            $plan->save();

            $updatedPlan = $plan;

            $completedReminder = Reminder::where('installation_id', $installation->id)
                ->where('maintenance_plan_id', $plan->id)
                ->where('status', 'pending')
                ->whereDate('due_date', '<=', $validated['serviced_on'])
                ->latest()
                ->first();

            if ($completedReminder) {
                $completedReminder->status = 'completed';
                $completedReminder->save();
            }
        }

        return response()->json([
            'message' => 'Service visit logged successfully',
            'service_visit' => $visit,
            'updated_plan' => $updatedPlan,
            'completed_reminder' => $completedReminder,
        ], 201);
    }
}
