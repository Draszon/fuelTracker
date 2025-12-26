<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

/**
 * Admin felhasználók kezelését végző controller.
 * 
 * Ez a controller felelős az új felhasználók létrehozásáért admin jogosultsággal.
 * Kezeli a felhasználói regisztrációs űrlap megjelenítését és az új felhasználók
 * adatbázisba történő mentését (beleértve a jelszó hashelését és admin státusz beállítását).
 */
class AdminUserController extends Controller
{
    /**
     * Új felhasználó létrehozása.
     * 
     * Validálja a bejövő adatokat (név, email, jelszó) és létrehoz egy új felhasználót
     * az adatbázisban. A jelszót hashelve tárolja, és beállítja az admin státuszt.
     * Sikeres létrehozás esetén visszairányít a dashboardra, hiba esetén hibaüzenetet jelenít meg.
     * 
     * @param  \Illuminate\Http\Request  $request A bejövő HTTP kérés objektum (name, email, password, isAdmin)
     * @return \Illuminate\Http\RedirectResponse Visszairányítás üzenettel
     */
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
            return redirect()->back()->with('message', 'Hiba a felhasználó regisztrációja közben: ' . $e->getMessage());
        }
    }
}
