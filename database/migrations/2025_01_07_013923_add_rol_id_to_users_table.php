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
        Schema::table('users', function (Blueprint $table) {
            //agregando una nueva columna
            $table->unsignedBigInteger('rol_id')->after('id'); //agregando el campo despues del id
            //agregando la foranea
            //$table->foreignId('rol_id')->constrained()->onDelete('cascade');
            $table->foreign('rol_id')->references('id')->on('rol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //eliminamos la columna
            $table->dropForeign(['rol_id']);
            $table->dropColumn('rol_id');
        });
    }
};
