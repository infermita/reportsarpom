<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\UserController;

Route::get('/', [DefaultController::class,'index'])->middleware('auth');
Route::post('/', [DefaultController::class,'index']);

Route::get('/login', [UserController::class,'login'])->name("login");;
Route::post('/login', [UserController::class,'login']);
Route::get('/logout', [UserController::class,'logout']);

Route::get('/users', [DefaultController::class,'users'])->middleware('auth');
Route::post('/users', [DefaultController::class,'users'])->middleware('auth');
Route::post('/delete', [DefaultController::class,'delete'])->middleware('auth');