<?php

namespace App\Http\Controllers;

use App\Models\CustomerCard;
use App\Http\Requests\StoreCustomerCardRequest;
use App\Http\Requests\UpdateCustomerCardRequest;

class CustomerCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerCardRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerCard $customerCard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerCard $customerCard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerCardRequest $request, CustomerCard $customerCard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerCard $customerCard)
    {
        //
    }
}
