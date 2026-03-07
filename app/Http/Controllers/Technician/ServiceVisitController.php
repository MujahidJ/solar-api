<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use App\Models\ServiceVisit;
use Illuminate\Http\Request;

class ServiceVisitController extends Controller
{
    public function store(Request $request, Installation $installation)
    {
        $user = $request->user();

        // must be assigned to this installation
        $isAssigned = $user->assignedInstallations()->where('installations.id', $installation->id)->exists();
        if (!$isAssigned) {
            return response()->json(['message' => 'Forbidden: not assigned to this installation'], 403);
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

        return response()->json($visit, 201);
    }
}