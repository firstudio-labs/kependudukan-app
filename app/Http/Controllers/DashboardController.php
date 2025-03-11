<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = ucfirst($user->role); // Capitalize first letter of role

        switch ($user->role) {
            case 'superadmin':
                return view('superadmin.index', compact('user', 'role'));
            case 'admin':
                return view('admin.index', compact('user', 'role'));
            case 'operator':
                return view('operator.index', compact('user', 'role'));
            default:
                return view('user.index', compact('user', 'role'));
        }
    }
}

