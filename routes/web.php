<?php

use Illuminate\Support\Facades\Route;

// Web

Route::get('/', function () {
    return view('welcome');
});
