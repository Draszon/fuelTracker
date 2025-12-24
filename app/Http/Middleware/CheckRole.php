<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user(); //A bejelentkezett felhasználó

        //Ellenőrzöm, hogy a felhasználó admin-e, ha nem, akkor hibaüzenettel visszadobom
        if (!$user || !$user->is_admin) {
            return redirect()->back()->with('message', 'Nincs jogosultságod ehhez az oldalhoz!');
        }

        return $next($request);
    }
}
