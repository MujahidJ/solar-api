<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Reminder::with(['installation:id,name,location', 'maintenancePlan:id,title,trigger_type'])
            ->where('client_id', $user->id)
            ->latest()
            ->paginate(20);
    }
}
