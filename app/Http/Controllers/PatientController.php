<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PatientService;
use App\Models\DiagnosticAssignment;
use App\Models\Diagnostic;
use Carbon\Carbon;
use DB;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            $patientService = new PatientService();
            $patient = $patientService->storePatient($data);

            return $patient;

        }catch (\Exception $e) {
            return response()->json([
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();

            $patientService = new PatientService();
            $patient = $patientService->updatePatient($id,$data);

            return $patient;

        }catch (\Exception $e) {
            return response()->json([
                'message' =>  $e->getMessage(),
            ],500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
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

            return response()->json([
                'message' => 'Paciente eliminado correctamente',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' =>  $e->getMessage(),
            ],500);
            
        }

        
    }

    public function assignment(Request $request)
    {
        $data = $request->all();
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

        $diagnosticAssignment = DiagnosticAssignment::create($data);

        return response()->json([
            'diagnosticAssignment' => $diagnosticAssignment,
        ], 201);
    }

    public function getPatients()
    {
        $patients = Patient::join('diagnotic_assignments', 'patients.id', '=', 'diagnotic_assignments.patient_id')
            ->join('diagnostics', 'diagnotic_assignments.diagnostic_id', '=', 'diagnostics.id')
            ->get();
        
        return response()->json([
                'patients' => $patients,
            ], 201);
    }

    public function searchPatients(Request $request)
    {
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $document = $request->get('document');

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

    public function getTopFiveDiagnostics()
    {
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
