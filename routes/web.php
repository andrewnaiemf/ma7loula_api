<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/test', function () {
    $user = User::find(21);
    Auth::login($user);
    return view('test-websocket');
});