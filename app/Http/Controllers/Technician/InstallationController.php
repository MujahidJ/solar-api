<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return $user->assignedInstallations()
            ->with(['client:id,name,email'])
            ->latest()
            ->paginate(20);
    }
}