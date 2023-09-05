<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DownPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        return view('content.DownPayment.ListDownPayment');
    }
}
