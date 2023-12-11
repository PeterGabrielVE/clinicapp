<?php

namespace App\Services;


use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\DiagnosticAssignment;
use App\Models\Diagnostic;
use Illuminate\Support\Facades\Validator;
use DB;

class PatientService
{

    public $patients;

    public function __construct()
    {

    }

    public function storePatient(array $data)
    {
        $validator = Validator::make($data, [
            'document' => 'required|string|unique:patients,document',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birth_date' => 'required|date',
            'email' => 'required|string|unique:patients,email',
            'phone' => 'required|string',
            'genre' => 'required|string|in:male,female',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            $response = [
                'status'  => false,
                'message' => $errorMessage,
            ];
            return response()->json($response, 401);
        }

        $exist = Patient::where('document', $data['document'])->first();
        if ($exist) {
            return response()->json([
                'message' => 'El paciente ya existe.',
            ], 409);
        }
        global $patient;

        DB::transaction(function() use ($data){
            $patient = Patient::create($data);
            $GLOBALS['patient'] = $patient;
        
        });

        return response()->json([
            'message' => 'Paciente agregado correctamente',
            'patient' => $patient,
        ], 201);
    }

    public function updatePatient($id,array $data)
    {
        $validator = Validator::make($data, [
            'document' => 'required|string|unique:patients',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birth_date' => 'required|date',
            'email' => 'required|string|unique:patients',
            'phone' => 'required|string',
            'genre' => 'required|string|in:male,female',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            $response = [
                'status'  => false,
                'message' => $errorMessage,
            ];
            return response()->json($response, 401);
        }
        global $patient1;
        $patient = Patient::find($id);
        if (!$patient) {
            return response()->json([
                'message' => 'El paciente no existe.',
            ], 409);
        }
        DB::transaction(function() use ($data,$patient){
        $patient->fill($data);
        $patient->save();

            $GLOBALS['patient1'] = $patient;
        });

        return response()->json([
            'message' => 'actualizado correctamente',
            'patient' => $patient1,
        ], 201);
    }

    public function destroyPatient($id){

        DB::transaction(function() use ($id){
            $patient = Patient::find($id);

            $assignments = $patient->assignments;
            $diagnostics = $patient->diagnostics;

            foreach ($assignments as $assignment) {
                $assignment->delete();
            }

            foreach ($diagnostics as $diagnostic) {
                $diagnostic = Diagnostic::find($diagnostic->id);
                $diagnostic->delete();
            }
                
            $patient->delete();
        });

        return response()->json([
            'message' => 'Paciente eliminado correctamente',
        ]);

    }

    public function assignmentDiagnostic(array $data){

        $validator = Validator::make($data, [
            'diagnostic_id' => 'required|integer',
            'patient_id' => 'required|integer',
            'creation' => 'required|date',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            $response = [
                'status'  => false,
                'message' => $errorMessage,
            ];
            return response()->json($response, 401);
        }
        
        global $diagnosticAssignment;

        DB::transaction(function() use ($data){

            $diagnosticAssignment = DiagnosticAssignment::create($data);
            $GLOBALS['diagnosticAssignment'] = $diagnosticAssignment;

        });

        return response()->json([
            'message' => 'Paciente asignado correctamente',
            'diagnosticAssignment' => $diagnosticAssignment,
        ], 201);

    }
}
