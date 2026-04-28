<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Mail::to('a230158ds@rvt.lv')
            ->send(new ContactMessage($data['name'], $data['email'], $data['message']));

        return response()->json(['message' => 'Sent']);
    }
}
