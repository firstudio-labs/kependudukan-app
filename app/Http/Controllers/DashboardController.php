<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index()
    {


    }

    public function superadmin()
    {
        return view('superadmin.index'); // Pastikan kamu memiliki view 'dokter_dashboard'
    }


}

