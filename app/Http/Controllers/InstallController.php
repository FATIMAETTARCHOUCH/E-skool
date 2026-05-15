<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InstallController extends Controller
{
    public function index()
    {
        if (User::count() > 0) {
            return redirect('/login')->with('info', 'System already installed.');
        }
        return view('install.index');
    }

    public function store(Request $request)
    {
        if (User::count() > 0) abort(403);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        return redirect('/login')->with('success', 'Admin account created. You can now login.');
    }
}
