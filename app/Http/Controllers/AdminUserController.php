<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index() {
        return inertia('Admin/Create');
    }

    public function create(Request $request) {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'is_admin'  => $request->isAdmin,
            ]);
            return redirect()->route('dashboard')->with('success', 'Sikeres regisztráció!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba a felhasználó egisztrációja közben: ' . $e->getMessage());
        }
    }
}
