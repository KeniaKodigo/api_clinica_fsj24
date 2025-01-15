<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PatientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//-> api

//Agrupando rutas protegidas
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/v1/patients', [PatientController::class, 'store']); //enviar 
    //Ruta con parametros
    Route::get('/v1/patients/{patientId}', [PatientController::class, 'patient_by_id']);
    //PUT o PATCH => actualizar data
    Route::patch('/v1/patients/{patientId}',[PatientController::class, 'update']);
    Route::get('/v1/patients-doctor', [AppointmentsController::class, 'get_patients_by_doctor']);

    //Ruta para cerrar sesion
    Route::post('/v1/logout', [AuthenticationController::class, 'logout']);
});

//rutas para los pacientes (get, post, put, patch, delete)
Route::get('/v1/patients', [PatientController::class, 'index']);

//rutas para las citas
Route::post('/v1/appointments', [AppointmentsController::class,'store']);
//rutas para el metodo de las fechas (parametros opcionales)
Route::get('/v1/appointments', [AppointmentsController::class, 'get_appointments']);

//Ruta para el login
Route::post('/v1/login', [AuthenticationController::class, 'login']);
//nosotros le podemos colocar nombre a las rutas


//ruta  
Route::get('/token', function(){
    return response()->json(['mensaje' => 'Necesitas un token'], 401);
})->name('login');

/**
 * url('/token)
 * route('login')
 */
