<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Reminder::with(['installation:id,name,location', 'maintenancePlan:id,title,trigger_type'])
            ->where('technician_id', $user->id)
            ->latest()
            ->paginate(20);
    }
}