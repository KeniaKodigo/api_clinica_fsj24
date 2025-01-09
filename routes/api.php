<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//-> api

//rutas para los pacientes (get, post, put, patch, delete)
Route::get('/v1/patients', [PatientController::class, 'index']);
Route::post('/v1/patients', [PatientController::class, 'store']); //enviar 
//Ruta con parametros
Route::get('/v1/patients/{patientId}', [PatientController::class, 'patient_by_id']);
//PUT o PATCH => actualizar data
Route::patch('/v1/patients/{patientId}',[PatientController::class, 'update']);

//rutas para las citas
Route::post('/v1/appointments', [AppointmentsController::class,'store']);