<?php

namespace Tests\Feature;

use App\Models\Appointments;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    //Limpia la base de datos para cada prueba
    //use RefreshDatabase;
    /**
     * A basic feature test example.
     * mocks => objetos simulados 
     * assertions => afirmaciones 
     */
    
    //testeando la ruta de obtener pacientes
    public function test_get_patients(){
        //crear datos quemados
        //Patient::factory(3)->create();

        $response = $this->getJson('api/v1/patients');
        $response->assertStatus(200); //si pasa o no pasa el test
        //$response->assertJsonCount(14); //si pasa
    }

    //test para confirmar la creacion de un paciente
    public function test_store(){

        //Patient::factory()->create();
        //creando un usuario
        $user = User::factory()->create();
        //crear un token
        $token = $user->createToken('test_token')->plainTextToken;

        $patient = [
            'name' => 'Kenia Paiz',
            'date_born' => '1998-08-19',
            'gender' => 'Femenino',
            'address' => 'San Ignacio',
            'phone' => '76543898',
            'email'=> 'kenia@example.com'
        ];

        $response = $this->withHeader('Authorization',"Bearer $token")->postJson('api/v1/patients',$patient);
        $response->assertStatus(201);
    }

    //testeando el endpoint de obtener pacientes por doctor
    public function test_patients_by_doctor(){
        //creando un doctor
        $user = User::factory()->create(['rol_id' => 1]);
        $patient = Patient::factory()->create();

        //creando una cita con datos quemados
        $appointment = new Appointments();
        $appointment->user_id = $user->id;
        $appointment->patient_id = $patient->id;
        $appointment->date_appointment = now();
        $appointment->time_appointment = '13:00';
        $appointment->reason = 'consulta general';
        $appointment->status = 'Pendiente';
        $appointment->save();

        //se asegura que haya un usuario autenticado
        $response = $this->actingAs($user)->getJson('api/v1/patients-doctor');
        $response->assertOk();
    }
}
