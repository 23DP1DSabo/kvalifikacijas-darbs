<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'surname'  => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        ]);
        $user->update($data);
        return response()->json(['user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);
        if (!Hash::check($data['current_password'], $request->user()->password)) {
            return response()->json(['errors' => ['current_password' => ['Nepareiza pašreizējā parole.']]], 422);
        }
        $request->user()->update(['password' => Hash::make($data['password'])]);
        return response()->json(['message' => 'ok']);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048|mimes:jpeg,jpg,png,webp,gif',
        ]);

        $user = $request->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['profile_picture' => $path]);

        return response()->json(['avatar_url' => Storage::url($path)]);
    }
}
