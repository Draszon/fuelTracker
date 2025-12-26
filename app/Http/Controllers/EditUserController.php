<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Hash;

class EditUserController extends Controller
{
    public function index() {
        $users = User::select('id','name', 'email', 'is_admin')->get();

        return Inertia::render('Admin/EditUsers', [
            'users' => $users,
        ]);
    }

    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();
    }

    public function updatePasswd(Request $request, $id) {
        $request->validate([
            'passwd' => 'required|string|min:8',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->passwd);
            $user->save();

            return redirect()->back()->with('message', 'Sikeres jelszómódosítás!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message' . 'Hiba a jelszó módosítása közben: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255',
            'is_admin'  => '',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->update($validated);
            return redirect()->back()->with('message', 'Sikeres frissítés!');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Hiba az adatok módosítása közben: ' . $e->getMessage());
        }
    }
}
