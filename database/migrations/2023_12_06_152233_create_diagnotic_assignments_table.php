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
        Schema::create('diagnotic_assignments', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Unique identifier. This field is generated automatically when anew diagnostic is created');
            $table->unsignedBigInteger('patient_id')->index('fk_diagnostic_assignments_patients_idx')->comment('Patient Identifier');
            $table->unsignedBigInteger('diagnostic_id')->index('fk_diagnostic_assignments_diagnostics_idx')->comment('Diagnostic Identifier');
            $table->string('observations', 255)->nullable()->comment('Diagnostic observations');
            $table->dateTime('creation')->required()->comment('Creation Date');
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('diagnostic_id')->references('id')->on('diagnostics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diagnotic_assignments');
    }
};
