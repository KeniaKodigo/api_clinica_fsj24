<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    //
    public function get_patients(){
        //select * from patients where id = 2

        //QUERY BUILDER => construir una consulta (manual)
        DB::table('patients')->get();
        DB::table('patients')->where('id', 2)->get();
        //

        //ORM ELOQUENT
        /**
         * - el utiliza modelos
         * - metodos mapeados => all(), find(), save(), update()
         */
        Patient::all(); //select * from patients
        Patient::find(2); //SELECT * FROM patients where id = 2

        //select name, date_born from patients where gender = 'Femenino'
        Patient::where('gender','Femenino')->select('name','date_born')->get();

        //between -> whereBetween
        Patient::where('id', 2)->get();
    }
}
