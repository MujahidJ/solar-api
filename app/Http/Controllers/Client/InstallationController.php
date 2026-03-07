<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return $user->installationsOwned()
            ->with(['technicians:id,name,email', 'maintenancePlans'])
            ->latest()
            ->paginate(20);
    }

    public function show(Request $request, Installation $installation)
    {
        $user = $request->user();

        if ((int) $installation->client_id !== (int) $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $installation->load(['technicians:id,name,email', 'maintenancePlans', 'serviceVisits']);
    }
}