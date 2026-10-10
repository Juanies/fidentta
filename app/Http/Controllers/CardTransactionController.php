<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardTransactionRequest;
use App\Http\Requests\UpdateCardTransactionRequest;
use App\Models\CardTransaction;

class CardTransactionController extends Controller
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
    public function store(StoreCardTransactionRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CardTransaction $cardTransaction): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CardTransaction $cardTransaction): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCardTransactionRequest $request, CardTransaction $cardTransaction): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CardTransaction $cardTransaction): void
    {
        //
    }
}
