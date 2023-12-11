<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PatientService;

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
            $patientService = new PatientService();
            $patient = $patientService->destroyPatient($id);

            return $patient;

        } catch (\Exception $e) {
            return response()->json([
                'message' =>  $e->getMessage(),
            ],500);
            
        }

    }

    public function assignment(Request $request)
    {
        try {
            $data = $request->all();

            $patientService = new PatientService();
            $patient = $patientService->assignmentDiagnostic($data);

            return $patient;

        }catch (\Exception $e) {
            return response()->json([
                'message' =>  $e->getMessage(),
            ],500);
        }
        
    }

    public function getPatients()
    {
        try {
            $patientService = new PatientService();
            $patients = $patientService->getPatients();

            return $patients;
        }catch (\Exception $e) {
            return response()->json([
                'message' =>  $e->getMessage(),
            ],500);
        }

    }

    public function searchPatients(Request $request)
    {
        try {

            $first_name = $request->get('first_name');
            $last_name = $request->get('last_name');
            $document = $request->get('document');
    
            $patientService = new PatientService();
            $patients = $patientService->searchPatients($first_name, $last_name, $document);

            return $patients;
        }catch (\Exception $e) {

            return response()->json([
                'message' =>  $e->getMessage(),
            ],500);
        }


    }

    public function getTopFiveDiagnostics()
    {
        try {

            $patientService = new PatientService();
            $patients = $patientService->getTopFiveDiagnostics();

            return $patients;
        }catch (\Exception $e) {
            
            return response()->json([
                'message' =>  $e->getMessage(),
            ],500);
        }
        

    }

}
