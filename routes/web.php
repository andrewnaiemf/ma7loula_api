<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/login', function (Request $req) {
    $password = $req->input('password');
    
    if ($password == 'karimkarim') {
        Auth::login(User::where('phone', '01119494098')->first());
    }

    if(Auth::user()){
        return Auth::user();
    }
});
