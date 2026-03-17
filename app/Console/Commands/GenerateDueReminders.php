<?php

namespace App\Console\Commands;

use App\Models\MaintenancePlan;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateDueReminders extends Command
{
    protected $signature = 'reminders:generate-due';
    protected $description = 'Generate due reminders from active maintenance plans';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        $plans = MaintenancePlan::with(['installation.client', 'installation.technicians'])
            ->where('active', true)
            ->whereIn('trigger_type', ['time', 'hybrid'])
            ->whereDate('next_due_date', '<=', $today)
            ->get();

        $count = 0;

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

            Reminder::create([
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

            $count++;
        }

        $this->info("Generated {$count} due reminder(s).");

        return self::SUCCESS;
    }
}
