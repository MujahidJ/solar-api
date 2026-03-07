<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use App\Models\User;
use Illuminate\Http\Request;

class InstallationAssignmentController extends Controller
{
    public function assign(Request $request, Installation $installation)
    {
        $validated = $request->validate([
            'technician_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $tech = User::findOrFail($validated['technician_id']);
        if ($tech->role !== 'technician') {
            return response()->json(['message' => 'technician_id must belong to a user with role=technician'], 422);
        }

        $installation->technicians()->syncWithoutDetaching([$tech->id]);

        return response()->json([
            'message' => 'Technician assigned',
            'installation' => $installation->load('technicians:id,name,email'),
        ]);
    }
}