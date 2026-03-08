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
        'title' => ['required', 'string', 'max:255'],
        'trigger_type' => ['required', 'in:time,condition,hybrid'],
        'interval_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
        'condition_rule' => ['nullable', 'string', 'max:255'],
        'next_due_date' => ['nullable', 'date'],
        'active' => ['sometimes', 'boolean'],
    ]);

    if (in_array($validated['trigger_type'], ['time', 'hybrid'], true) && empty($validated['interval_days'])) {
        return response()->json([
            'message' => 'interval_days is required for time or hybrid trigger types'
        ], 422);
    }

    if (in_array($validated['trigger_type'], ['condition', 'hybrid'], true) && empty($validated['condition_rule'])) {
        return response()->json([
            'message' => 'condition_rule is required for condition or hybrid trigger types'
        ], 422);
    }

    MaintenancePlan::where('installation_id', $installation->id)->update(['active' => false]);

    $plan = MaintenancePlan::create([
        'installation_id' => $installation->id,
        'title' => $validated['title'],
        'trigger_type' => $validated['trigger_type'],
        'interval_days' => $validated['interval_days'] ?? null,
        'condition_rule' => $validated['condition_rule'] ?? null,
        'next_due_date' => $validated['next_due_date'] ?? null,
        'active' => $validated['active'] ?? true,
    ]);

    return response()->json($plan, 201);
}
}