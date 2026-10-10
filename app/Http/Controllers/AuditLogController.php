<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuditLogRequest;
use App\Http\Requests\UpdateAuditLogRequest;
use App\Models\AuditLog;

class AuditLogController extends Controller
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
    public function store(StoreAuditLogRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AuditLog $auditLog): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AuditLog $auditLog): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuditLogRequest $request, AuditLog $auditLog): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuditLog $auditLog): void
    {
        //
    }
}
