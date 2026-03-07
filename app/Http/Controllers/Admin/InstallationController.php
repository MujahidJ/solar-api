<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use App\Models\User;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
    public function index()
    {
        return Installation::with(['client:id,name,email', 'technicians:id,name,email'])
            ->latest()->paginate(20);
    }

    public function show(Installation $installation)
    {
        return $installation->load(['client:id,name,email', 'technicians:id,name,email', 'maintenancePlans', 'serviceVisits']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'installation_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $client = User::findOrFail($validated['client_id']);
        if ($client->role !== 'client') {
            return response()->json(['message' => 'client_id must belong to a user with role=client'], 422);
        }

        $installation = Installation::create($validated);

        return response()->json($installation->load('client:id,name,email'), 201);
    }
}
