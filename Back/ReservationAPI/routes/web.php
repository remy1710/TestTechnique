<?php

use App\Http\Controllers\SalleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

