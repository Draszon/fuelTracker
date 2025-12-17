<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function create() {
        return inertia('Admin/Create');
    }
}
