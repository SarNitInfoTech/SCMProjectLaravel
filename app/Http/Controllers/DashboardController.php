<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Unit;
use App\Models\Project;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard Overview';

        $stats = [
            'departments' => Department::count(),
            'units'       => Unit::count(),
            'projects'    => Project::count(),
            'users'       => User::count(),
        ];

        return view('pages.dashboard.dashboard', compact('title', 'stats'));
    }
}
