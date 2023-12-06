<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Unique identifier. This field is generated automatically when a new patient is created');
            $table->string('document', 20)->unique()->required()->comment('Identification document');
            $table->string('first_name', 255)->required()->comment('Patient first name');
            $table->string('last_name', 255)->required()->comment('Patient last name');
            $table->date('birth_date')->required()->comment('Patient Birthday');
            $table->string('email', 255)->unique()->required()->comment('Contact email');
            $table->string('phone', 20)->required()->comment('Contact phone');
            $table->string('genre', 30)->required()->comment('Patient Genre ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
