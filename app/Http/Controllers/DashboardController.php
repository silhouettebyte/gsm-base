<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
	{
		$data = Location::orderBy('created_at', 'desc')->first();
		return view('dashboard.welcome', ['latest' => $data]);
	}
}
