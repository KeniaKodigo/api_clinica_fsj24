<?php

use App\Http\Controllers\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//-> api

//rutas para los pacientes (get, post, put, patch, delete)
Route::get('/v1/patients', [PatientController::class, 'index']);
Route::post('/v1/patients', [PatientController::class, 'store']);