<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenancePlan;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReminderGenerationController extends Controller
{
    public function generateDue(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $plans = MaintenancePlan::with(['installation.client', 'installation.technicians'])
            ->where('active', true)
            ->whereIn('trigger_type', ['time', 'hybrid'])
            ->whereDate('next_due_date', '<=', $today)
            ->get();

        $created = [];

        foreach ($plans as $plan) {
            $installation = $plan->installation;

            if (!$installation || !$installation->client) {
                continue;
            }

            $alreadyExists = Reminder::where('maintenance_plan_id', $plan->id)
                ->where('due_date', $plan->next_due_date)
                ->where('status', 'pending')
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $assignedTech = $installation->technicians()->first();

            $reminder = Reminder::create([
                'installation_id' => $installation->id,
                'maintenance_plan_id' => $plan->id,
                'client_id' => $installation->client_id,
                'technician_id' => $assignedTech?->id,
                'message' => sprintf(
                    'Maintenance due for %s: %s',
                    $installation->name,
                    $plan->title
                ),
                'due_date' => $plan->next_due_date,
                'status' => 'pending',
                'trigger_source' => $plan->trigger_type === 'hybrid' ? 'hybrid' : 'time',
            ]);

            $created[] = $reminder;
        }

        return response()->json([
            'message' => 'Due reminders generated successfully',
            'count' => count($created),
            'reminders' => $created,
        ]);
    }
}
