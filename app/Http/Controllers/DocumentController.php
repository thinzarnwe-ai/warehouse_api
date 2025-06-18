<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee-id' => 'required|string|max:50',
            'phone' => 'required|string|max:15',
            'time' => 'required',
            'remarks' => 'nullable|string',
            'place' => 'nullable|string',
            // 'location' => 'required|string',
            'departureBranch.lat' => 'required|numeric',
            'departureBranch.lng' => 'required|numeric',
            'arrivalLocation.lat' => 'required|numeric',
            'arrivalLocation.lng' => 'required|numeric',
        ]);

        $employeeInformation = new Document();
        $employeeInformation->name = $validated['name'];
        $employeeInformation->employee_id = $validated['employee-id'];
        $employeeInformation->phone = $validated['phone'];
        $employeeInformation->time = $validated['time'];
        $employeeInformation->remark = $validated['remarks'];
        $employeeInformation->place = $validated['place'];
        $employeeInformation->departure_branch = 1;
        // $employeeInformation->location = $validated['location'];
        // $employeeInformation->departure_latitude = $validated['departureBranch']['lat'];
        // $employeeInformation->departure_longitude = $validated['departureBranch']['lng'];
        $employeeInformation->arrival_latitude = $validated['arrivalLocation']['lat'];
        $employeeInformation->arrival_longitude = $validated['arrivalLocation']['lng'];
        $employeeInformation->save();

        return response()->json(['message' => 'Employee information saved successfully'], 200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
