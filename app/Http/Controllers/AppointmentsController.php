<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/**
 * @OA\Tag(name="Appointments", description="API for managing appointments")
 */

class AppointmentsController extends Controller
{
    //metodo para guardar una cita
    /**
     * @OA\Post(
     *     path="/api/v1/appointments",
     *     summary="Register a new appointment",
     *     tags={"Appointments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="patient_id", type="integer", example=1, description="ID del paciente"),
     *             @OA\Property(property="user_id", type="integer", example=2, description="ID del usuario (doctor)"),
     *             @OA\Property(property="date_appointment", type="string", format="date", example="2025-01-20", description="Fecha de la cita"),
     *             @OA\Property(property="time_appointment", type="string", format="time", example="14:30", description="Hora de la cita en formato 24 horas"),
     *             @OA\Property(property="reason", type="string", example="Consulta general", description="Motivo de la cita")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment successfully registered",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Successfully registered"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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
    /**
     * @OA\Get(
     *     path="/api/v1/appointments",
     *     summary="Get appointments in a date range",
     *     tags={"Appointments"},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Fecha de inicio (opcional)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-01-10")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Fecha de fin (opcional)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2025-02-15")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de citas",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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
    /**
     * @OA\Get(
     *     path="/api/v1/patients-doctor",
     *     summary="Get patients assigned to a doctor",
     *     tags={"Appointments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of patients assigned to the doctor",
     *         @OA\JsonContent(type="array", @OA\Items(
     *             @OA\Property(property="patient", type="string", example="Juan Pérez"),
     *             @OA\Property(property="date_born", type="string", format="date", example="1990-05-15"),
     *             @OA\Property(property="gender", type="string", example="Masculino"),
     *             @OA\Property(property="doctor", type="string", example="Dr. Carlos López")
     *         ))
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *         @OA\JsonContent(@OA\Property(property="mensaje", type="string", example="Solo doctores tienen acceso a esta información"))
     *     )
     * )
     */
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
