<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/**
 * @OA\Tag(name="Patients", description="API for managing patients")
 */

class PatientController extends Controller
{
    //Obteniendo todos los pacientes
    /**
     * @OA\Get(
     *     path="/api/v1/patients",
     *     summary="Get all patients",
     *     description="Returns a list of all registered patients",
     *     tags={"Patients"},
     *     @OA\Response(
     *         response=200,
     *         description="List of patients successfully obtained"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No patients registered"
     *     )
     * )
     */
    public function index(){
        $patients = Patient::all(); //[]

        if(count($patients) > 0){
            return response()->json($patients, 200);
        }
        return response()->json([], 200);
    }

    //metodo para guardar un paciente
    /**
     * @OA\Post(
     *     path="/api/v1/patients",
     *     summary="Register a new patient",
     *     description="Registers a patient in the database",
     *     tags={"Patients"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "date_born", "gender", "address", "phone"},
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="date_born", type="string", format="date", example="2000-01-01"),
     *             @OA\Property(property="gender", type="string", enum={"Masculino", "Femenino"}, example="Masculino"),
     *             @OA\Property(property="address", type="string", example="Calle Falsa 123"),
     *             @OA\Property(property="phone", type="string", example="12345678"),
     *             @OA\Property(property="email", type="string", example="juan.perez@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Patient successfully registered"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request){
        //validando la entrada de datos del usuario
        $validator = Validator::make($request->all(), [
            //reglas para validar
            'name' => 'required|string|max:50',
            'date_born' => 'required|date_format:Y-m-d',
            'gender' => 'required|in:Masculino,Femenino',
            'address' => 'required|string',
            //validano que el telefono y correo no se repita en la bd
            'phone' => 'required|digits:8|unique:patients,phone',
            'email' => 'email|nullable|unique:patients,email'
        ]);

        //validando si se rompe las reglas de entrada de datos
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }
        
        //nueva instancia
        $patient = new Patient();
        $patient->name = $request->input('name');
        $patient->date_born = $request->input('date_born');
        $patient->gender = $request->input('gender');
        $patient->address = $request->input('address');
        $patient->phone = $request->input('phone');
        $patient->email = $request->input('email');
        $patient->save(); //insert into

        return response()->json(['message' => 'Successfully registeres'], 201);
    }

    //obtener un paciente por su id
    /**
     * @OA\Get(
     *     path="/api/v1/patients/{patientId}",
     *     summary="Obtain a patient by ID",
     *     description="Obtains the information of a specific patient by patient ID",
     *     tags={"Patients"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="patientId",
     *         in="path",
     *         description="Patient ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient successfully found"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient not found"
     *     )
     * )
     */
    public function patient_by_id($id){
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        //validando si se rompe las reglas de entrada de datos
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        //select * from patients where id = $id
        //Patient::where('id',$id)->get(); //query builder + orm
        $patient = Patient::find($id); //{}, null

        if($patient != null){
            return response()->json($patient, 200);
        }
        return response()->json(['message' => 'Patient not found'], 404);
    }

    //metodo para actualizar un paciente
    /**
     * @OA\Patch(
     *     path="/api/v1/patients/{patientId}",
     *     summary="Update patient information",
     *     description="Update existing patient data",
     *     tags={"Patients"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="patientId",
     *         in="path",
     *         description="Patient ID to be updated",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "address", "phone"},
     *             @OA\Property(property="name", type="string", example="Juan Pérez Actualizado"),
     *             @OA\Property(property="address", type="string", example="Avenida Siempre Viva"),
     *             @OA\Property(property="phone", type="string", example="87654321"),
     *             @OA\Property(property="email", type="string", example="juan.actualizado@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient successfully updated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error"
     *     )
     * )
     */
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            //reglas para validar
            'name' => 'required|string|max:50',
            'address' => 'required|string',
            'phone' => 'required|digits:8',
            'email' => 'email|nullable'
        ]);

        //validando si se rompe las reglas de entrada de datos
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }
        
        //nueva instancia
        $patient = Patient::find($id); //{}
        $patient->name = $request->input('name');
        $patient->address = $request->input('address');
        $patient->phone = $request->input('phone');
        $patient->email = $request->input('email');
        $patient->update(); //UPDATE SET....

        return response()->json(['message' => 'Correctly updated'], 200);
    }
}

//mocks