<?php

namespace App\Services;


use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\DiagnosticAssignment;
use App\Models\Diagnostic;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
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
            ], 419);
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
            ], 419);
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

    public function getPatients(){
        $patients = Patient::join('diagnotic_assignments', 'patients.id', '=', 'diagnotic_assignments.patient_id')
        ->join('diagnostics', 'diagnotic_assignments.diagnostic_id', '=', 'diagnostics.id')
        ->get();
    
        return response()->json([
                'patients' => $patients,
            ], 201);
    }

    public function searchPatients($first_name, $last_name, $document){
        $patients = Patient::join('diagnotic_assignments', 'patients.id', '=', 'diagnotic_assignments.patient_id')
        ->join('diagnostics', 'diagnotic_assignments.diagnostic_id', '=', 'diagnostics.id')
        ->get();
    
        $patients = Patient::where(function ($query) use ($first_name, $last_name, $document) {
            if ($first_name) {
                $query->where('first_name', 'like', '%' . $first_name . '%');
            }

            if ($last_name) {
                $query->where('last_name', 'like', '%' . $last_name . '%');
            }

            if ($document) {
                $query->where('document', $document);
            }
        })->get();
        
        return response()->json([
            'patients' => $patients,
        ], 201);
    }

    public function getTopFiveDiagnostics(){
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        
        $sixMonthsAgo = $now->subDays(180);
        $sixMonthsAgo = $sixMonthsAgo->format('Y-m-d');

        $diagnostics = DB::table('diagnotic_assignments')
            ->join('patients', 'diagnotic_assignments.patient_id', '=', 'patients.id')
            ->whereBetween('diagnotic_assignments.creation', [$sixMonthsAgo, $today])
            ->select('diagnotic_assignments.diagnostic_id', DB::raw('count(*) as count'))
            ->groupBy('diagnotic_assignments.diagnostic_id')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        return response()->json([
                'diagnostics' => $diagnostics,
            ], 201);    
    }

}
