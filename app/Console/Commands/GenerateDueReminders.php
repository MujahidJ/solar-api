<?php

namespace App\Console\Commands;

use App\Services\ReminderGenerationService;
use Illuminate\Console\Command;

class GenerateDueReminders extends Command
{
    protected $signature = 'reminders:generate-due';
    protected $description = 'Generate due reminders from active maintenance plans';

    public function __construct(
        protected ReminderGenerationService $reminderGenerationService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $result = $this->reminderGenerationService->generateDueReminders();

        $this->info("Generated {$result['count']} due reminder(s).");

        return self::SUCCESS;
    }
}