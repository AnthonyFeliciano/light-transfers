<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','min:3','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'document' => ['required','regex:/^\d{11,14}$/','unique:users,document'],
            'role'     => ['required','in:user,merchant'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $data['document'] = preg_replace('/\D/', '', $data['document']);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'document' => $data['document'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        Wallet::create(['user_id' => $user->id, 'balance' => 0]);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard');

    }
}
