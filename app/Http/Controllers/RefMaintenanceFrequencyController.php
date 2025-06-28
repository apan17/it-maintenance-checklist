<?php
namespace App\Http\Controllers;

use App\Models\RefComponent;
use App\Models\RefMaintenanceFrequency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RefMaintenanceFrequencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return RefMaintenanceFrequency::queryable()->extendPaginate();
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
                'name' => 'required|string',
            ];

            $message = [
                'name.required' => 'Name is required.',
                'name.string'   => 'Name must be a string.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            $refMaintenanceFrequency = RefMaintenanceFrequency::create([
                'name' => $request->name,
            ]);

            DB::commit();
            return $this->success('Maintenance Frequency successfully created.');
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
            $refMaintenanceFrequency = RefMaintenanceFrequency::findOrFail($id);
            return $this->success('Maintenance Frequency retrieved successfully.', $refMaintenanceFrequency);
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
                'name' => 'required|string',
            ];

            $message = [
                'name.required' => 'Name is required.',
                'name.string'   => 'Name must be a string.',
            ];

            $validator = Validator::make($request->all(), $rules);
            abort_if($validator->fails(), 422, $validator->errors()->first());

            $refMaintenanceFrequency = RefMaintenanceFrequency::findOrFail($id);

            RefComponent::where('maintenance_frequency', $refMaintenanceFrequency->name)
                ->update(['maintenance_frequency' => $request->name]);
            
            $refMaintenanceFrequency->update([
                'name' => $request->name,
            ]);

            DB::commit();
            return $this->success('Maintenance Frequency successfully updated.');
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
            $refMaintenanceFrequency = RefMaintenanceFrequency::findOrFail($id);
            $refMaintenanceFrequency->delete();

            DB::commit();
            return $this->success('Maintenance Frequency successfully deleted.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
