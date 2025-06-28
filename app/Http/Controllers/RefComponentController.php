<?php

namespace App\Http\Controllers;

use App\Models\RefComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RefComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return RefComponent::queryable()->extendPaginate();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'maintenance_frequency' => 'required|string',
                'name' => 'required|string',
            ];

            $message = [
                'maintenance_frequency.required' => 'Maintenance frequency is required.',
                'maintenance_frequency.string' => 'Maintenance frequency must be a string.',
                'name.required' => 'Name is required.',
                'name.string' => 'Name must be a string.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            $refComponent = RefComponent::create([
                'maintenance_frequency' => $request->maintenance_frequency,
                'name' => $request->name,
            ]);

            DB::commit();
            return $this->success('Component successfully created.');
        } catch (\Exception $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $refComponent = RefComponent::findOrFail($id);
            return $this->success('Component retrieved successfully.', $refComponent);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'maintenance_frequency' => 'required|string',
                'name' => 'required|string',
            ];

            $message = [
                'maintenance_frequency.required' => 'Maintenance frequency is required.',
                'maintenance_frequency.string' => 'Maintenance frequency must be a string.',
                'name.required' => 'Name is required.',
                'name.string' => 'Name must be a string.',
            ];

            $validator = Validator::make($request->all(), $rules);
            abort_if($validator->fails(), 422, $validator->errors()->first());
            $refComponent = RefComponent::findOrFail($id);
            $refComponent->update([
                'maintenance_frequency' => $request->maintenance_frequency,
                'name' => $request->name,
            ]);

            DB::commit();
            return $this->success('Component successfully updated.');
        } catch (\Exception $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $refComponent = RefComponent::findOrFail($id);
            $refComponent->delete();

            DB::commit();
            return $this->success('Component successfully deleted.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
