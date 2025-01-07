<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id(); //primary key
            // $table->foreignId('patient_id')->references('id')->on('patients')->onDelete('cascade');
            // $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            //creando 2 foraneas
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date_appointment');
            $table->time('time_appointment');
            $table->text('reason');
            $table->string('status', 50); //pendiente, confirmada o cancelada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
