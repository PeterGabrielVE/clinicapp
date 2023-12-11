<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\DiagnosticAssignment;
use App\Models\Diagnostic;
use DB;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('diagnotic_assignments')->delete();
        DB::table('patients')->delete();
        DB::table('diagnostics')->delete();

        // Crea algunos pacientes
        Patient::create([
            'document' => '1111111',
            'first_name' => 'Juan Pérez',
            'last_name' => 'Juan Pérez',
            'birth_date' => '1990-01-01',
            'email' => 'juan.perez@example.com',
            'phone' => '123-456-7890',
            'genre' => 'male',
        ]);
        Patient::create([
            'document' => '2222222',
            'first_name' => 'Maria',
            'last_name' => 'Lopez',
            'birth_date' => '1985-02-02',
            'email' => 'maria.lopez@example.com',
            'phone' => '987-654-3210',
            'genre' => 'female'

        ]);

        Diagnostic::create([
            'name' => 'Gripe',
        ]);
        Diagnostic::create([
            'name' => 'Resfriado',
        ]);
        Diagnostic::create([
            'name' => 'Dolor de cabeza',
        ]);
        Diagnostic::create([
            'name' => 'Infección de oído',
        ]);
        Diagnostic::create([
            'name' => 'Bronquitis',
        ]);

        $patient_id1 = Patient::where('document','1111111')->value('id');
        $patient_id2 = Patient::where('document','2222222')->value('id');

        $diagnostic1 = Diagnostic::where('name','Gripe')->value('id');
        $diagnostic2 = Diagnostic::where('name','Resfriado')->value('id');
        $diagnostic3 = Diagnostic::where('name','Dolor de cabeza')->value('id');
        $diagnostic4 = Diagnostic::where('name','Infección de oído')->value('id');
        $diagnostic5 = Diagnostic::where('name','Bronquitis')->value('id');

        DiagnosticAssignment::create([
            'patient_id' => $patient_id1,
            'diagnostic_id' => $diagnostic1,
            'creation' => '2023-07-20',
        ]);
        DiagnosticAssignment::create([
            'patient_id' => $patient_id2,
            'diagnostic_id' => $diagnostic2,
            'creation' => '2023-07-21',
        ]);
        DiagnosticAssignment::create([
            'patient_id' => $patient_id1,
            'diagnostic_id' => $diagnostic3,
            'creation' => '2023-07-22',
        ]);
        DiagnosticAssignment::create([
            'patient_id' => $patient_id2,
            'diagnostic_id' => $diagnostic4,
            'creation' => '2023-07-23',
        ]);
        DiagnosticAssignment::create([
            'patient_id' => $patient_id1,
            'diagnostic_id' => $diagnostic5,
            'creation' => '2023-07-24',
        ]);


    }
}