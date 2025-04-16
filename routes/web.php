<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\UserController;

Route::get('/', [DefaultController::class,'index']);//->middleware('auth');
Route::post('/', [DefaultController::class,'index']);

Route::get('/login', [UserController::class,'login']);
Route::post('/login', [UserController::class,'login']);
Route::get('/logout', [UserController::class,'logout']);