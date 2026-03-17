<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $query = Reminder::with([
            'installation:id,name,location',
            'maintenancePlan:id,title,trigger_type',
            'client:id,name,email',
            'technician:id,name,email',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('due_today')) {
            $query->whereDate('due_date', $today);
        }

        if ($request->boolean('overdue')) {
            $query->whereDate('due_date', '<', $today)
                ->where('status', 'pending');
        }

        return $query->paginate(20);
    }
}