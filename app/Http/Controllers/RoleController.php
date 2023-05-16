<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct() {
        $this->middleware('can:read role');
    }

    public function index()
    {
        //
        // $this->authorize('read role');
        return view('roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $this->authorize('read role');
        return 'create page';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //membuat
        $this->authorize('create role');
        return "store";
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //menampilkan
        return "show by id";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //menampilkan edit
        $this->authorize('update role');
        return "edit by id";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //submit edit
        $this->authorize('update role');
        return "update by id";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //hapus by id
        $this->authorize('delete role');
        return "destroy";
    }
}
