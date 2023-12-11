<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\DiagnosticAssignment;
use App\Models\Diagnostic;
use Illuminate\Support\Facades\Validator;
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
        dd('hola');
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
        $data = $request->all();

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

        $patient = Patient::where('document', $data['document'])->first();
        if ($patient) {
            return response()->json([
                'message' => 'El paciente ya existe.',
            ], 409);
        }

        $patient = Patient::create($data);

        return response()->json([
            'patient' => $patient,
        ], 201);
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
       // dd($request->all());
        $validator = Validator::make($request->all(), [
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

        $patient = Patient::find($id);
        if (!$patient) {
            return response()->json([
                'message' => 'El paciente no existe.',
            ], 409);
        }

        $patient->fill($request->all());
        $patient->save();

        return response()->json([
            'patient' => $patient,
        ], 201);
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
            ]);
            
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
