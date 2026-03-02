<?php

use Illuminate\Support\Facades\Route;

// Web Routes
// url: 127.0.0.1:8000/events/events-dashboard
Route::get('/', function () {
    return view('Events.event-dashboard-page');
});
