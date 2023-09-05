<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckInCheckOutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        return view('content.CheckInCheckOut.ListCheckInCheckOut');
    }
}
