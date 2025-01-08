<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    //el modelo hace uso de datos quemados
    use HasFactory;
    protected $table = 'patients';
}
