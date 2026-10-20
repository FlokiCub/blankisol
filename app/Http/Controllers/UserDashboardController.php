<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Show the user panel.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user && $user->isShopAdmin()) {
            return redirect()->route('shop.admin.dashboard');
        }

        return view('dashboard', compact('user'));
    }
}
