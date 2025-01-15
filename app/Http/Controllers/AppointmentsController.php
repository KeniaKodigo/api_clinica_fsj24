<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentsController extends Controller
{
    //metodo para guardar una cita
    public function store(Request $request){
        //validando la entrada de datos del usuario
        $validator = Validator::make($request->all(), [
            //validamos que el id del paciente y el usuario existan en la bd
            'patient_id' => 'required|exists:patients,id',
            'user_id' => 'required|exists:users,id',
            //validamos que la fecha de la cita debe ser posterior a la fecha actual
            'date_appointment' => 'required|date_format:Y-m-d|after_or_equal:today',
            //valida el formato de horas por 24 horas
            'time_appointment' => 'required|date_format:H:i',
            'reason' => 'required|string'
        ]);

        //validando si se rompe las reglas de entrada de datos
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }
        
        //nueva instancia
        $appointment = new Appointments();
        $appointment->patient_id = $request->input('patient_id');
        $appointment->user_id = $request->input('user_id');
        $appointment->date_appointment = $request->input('date_appointment');
        $appointment->time_appointment = $request->input('time_appointment');
        $appointment->reason = $request->input('reason');
        $appointment->status = "Pendiente";
        $appointment->save(); //insert into

        return response()->json(['message' => 'Successfully registered'], 201);
    }

    //SELECT * FROM appointments WHERE date_appointment BETWEEN  "2025-01-10" AND "2025-02-15"
    public function get_appointments(Request $request){
        //validando las fechas
        $validator = Validator::make($request->all(), [
            'start_date' => 'date|nullable|date_format:Y-m-d',
            'end_date' => 'date|nullable|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        //validando si se rompe las reglas de entrada de datos
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        //obteniendo las citas
        $query_appointments = Appointments::select('*');

        //validando parametros opcionales
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        if($start_date && $end_date){
            $query_appointments->whereBetween('date_appointment', [$start_date, $end_date]);
        }
        
        $data = $query_appointments->get();
        return response()->json($data, 200);
    }

    //todo los pacientes en base al doctor
    //validacion

    public function get_patients_by_doctor(Request $request){
        $user = $request->user();
        /**select patients.name as patient, patients.date_born, users.name as doctor from appointments 
        inner join users on appointments.user_id = users.id 
        inner join patients on appointments.patient_id = patients.id where users.id = 1; */

        if($user->rol_id !== 1){
            return response()->json(['mensaje' => 'Solo doctores tiene acceso a esta informacion'], 403);
        }

        $data = Appointments::select('patients.name as patient','patients.date_born','patients.gender','users.name as doctor')->join('users','appointments.user_id','users.id')->join('patients','appointments.patient_id','patients.id')->where('users.id',$user->id)->get();

        return response()->json($data, 200);
    }
}
