<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Hash;

class EditUserController extends Controller
{
    /**
     * Felhasználó szerkesztő oldal megjelenítése.
     *
     * - Lekéri az összes felhasználó alapvető adatait
     * - Átküldi az adatokat az Inertia nézetnek
     */
    public function index() {
        $users = User::select('id','name', 'email', 'is_admin')->get();

        return Inertia::render('Admin/EditUsers', [
            'users' => $users,
        ]);
    }

    /**
     * Felhasználó törléséért felel az adatbázisból.
     *
     * A kiválasztott felhasználó id alapján való törlését végzi.
     */
    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();
    }

    /**
     * Felhasználó jelszavának frissítése.
     *
     * - Validálja a jelszót (minimum 8 karakter)
     * - Hash-eli és menti az új jelszót
     * - Visszajelzést küld a művelet sikerességéről
     */
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

    /**
     * Felhasználó adatainak frissítése.
     *
     * - Név, email és admin jogosultság módosítására szolgál
     * - Validálja a bemeneti adatokat
     * - Visszajelzést küld a művelet sikerességéről
     */
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
