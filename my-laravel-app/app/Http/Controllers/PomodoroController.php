<?php

namespace App\Http\Controllers;

use App\Models\Pomodoro;
use Illuminate\Http\Request;

class PomodoroController extends Controller
{
    public function getSettings(Request $request)
    {
        $settings = Pomodoro::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['work_minutes' => 25, 'break_minutes' => 5, 'long_break_minutes' => 15]
        );

        return response()->json([
            'pomo'  => $settings->work_minutes,
            'short' => $settings->break_minutes,
            'long'  => $settings->long_break_minutes,
        ]);
    }

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'pomo'  => 'required|integer|min:1|max:120',
            'short' => 'required|integer|min:1|max:60',
            'long'  => 'required|integer|min:1|max:120',
        ]);

        Pomodoro::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'work_minutes'       => $data['pomo'],
                'break_minutes'      => $data['short'],
                'long_break_minutes' => $data['long'],
            ]
        );

        return response()->json(['message' => 'Saved.']);
    }
}
