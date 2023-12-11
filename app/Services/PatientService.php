<?php

namespace App\Services;


use Illuminate\Http\Request;
use App\Models\Patient;
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


}
