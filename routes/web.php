<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about-app', function () {
    return view('about-app');
});

Route::get('/privacy-policy', function () {
    return view('privacy-and-policy');
});

Route::get('/delete-account', function(){
    return view('delete_account');    
});

Route::post('/delete-account', function(){
    return view('delete_account')->with('message', 'We have received your request and your data will completely deleted from our servers in the next 48 hours.');
});