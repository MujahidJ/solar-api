<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationTokenController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expo_push_token' => ['required', 'string'],
        ]);

        $user = $request->user();
        $user->expo_push_token = $validated['expo_push_token'];
        $user->save();

        return response()->json([
            'message' => 'Expo push token saved successfully',
            'expo_push_token' => $user->expo_push_token,
        ]);
    }
}