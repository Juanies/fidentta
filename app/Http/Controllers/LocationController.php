<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): void
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): void
    {
        //
    }
}
