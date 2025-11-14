<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ClassCalendarController extends Controller
{
    public function index(): View
    {
        return view('coach.calendar');
    }
}
