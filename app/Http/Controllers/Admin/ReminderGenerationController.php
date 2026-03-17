<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReminderGenerationService;
use Illuminate\Http\Request;

class ReminderGenerationController extends Controller
{
    public function __construct(
        protected ReminderGenerationService $reminderGenerationService
    ) {}

    public function generateDue(Request $request)
    {
        $result = $this->reminderGenerationService->generateDueReminders();

        return response()->json([
            'message' => 'Due reminders generated successfully',
            'count' => $result['count'],
            'reminders' => $result['reminders'],
        ]);
    }
}
