<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use App\Models\MaintenancePlan;
use Illuminate\Http\Request;

class MaintenancePlanController extends Controller
{
    public function store(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'interval_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'next_due_date' => ['required', 'date'],
            'active' => ['sometimes', 'boolean'],
        ]);

        // optionally deactivate old plans if you want only one active:
        MaintenancePlan::where('installation_id', $installation->id)->update(['active' => false]);

        $plan = MaintenancePlan::create([
            'installation_id' => $installation->id,
            'interval_days' => $validated['interval_days'],
            'next_due_date' => $validated['next_due_date'],
            'active' => $validated['active'] ?? true,
        ]);

        return response()->json($plan, 201);
    }
}