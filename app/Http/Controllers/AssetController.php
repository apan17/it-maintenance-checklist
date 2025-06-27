<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return Asset::queryable()->extendPaginate();
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
                'component_id' => 'required|exists:components,id',
                'serial_no' => 'required|string|max:255|unique:assets,serial_no',
                'name' => 'required|string',
                'location' => 'required|string',
                'status' => 'required|string',
                'procedure' => 'nullable|string',
            ];

            $message = [
                'component_id.required' => 'Component is required.',
                'component_id.exists' => 'Component does not exist.',
                'serial_no.required' => 'Serial number is required.',
                'serial_no.string' => 'Serial number must be a string.',
                'serial_no.max' => 'Serial number must not exceed 255 characters.',
                'serial_no.unique' => 'Serial number must be unique.',
                'name.required' => 'Name is required.',
                'name.string' => 'Name must be a string.',
                'location.required' => 'Location is required.',
                'location.string' => 'Location must be a string.',
                'status.required' => 'Status is required.',
                'status.string' => 'Status must be a string.',
                'procedure.string' => 'Procedure must be a string.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            $asset = Asset::create([
                'component_id' => $request->component_id,
                'serial_no' => $request->serial_no,
                'name' => $request->name,
                'location' => $request->location,
                'status' => $request->status,
                'procedure' => $request->procedure,
            ]);

            DB::commit();
            return $this->success('Asset successfully created.');
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
            $asset = Asset::findOrFail($id);
            return $this->success('Asset retrieved successfully.', $asset);
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
                'component_id' => 'required|exists:components,id',
                'serial_no' => 'required|string|max:255|unique:assets,serial_no,' . $id,
                'name' => 'required|string',
                'location' => 'required|string',
                'status' => 'required|string',
                'procedure' => 'nullable|string',
            ];

            $message = [
                'component_id.required' => 'Component is required.',
                'component_id.exists' => 'Component does not exist.',
                'serial_no.required' => 'Serial number is required.',
                'serial_no.string' => 'Serial number must be a string.',
                'serial_no.max' => 'Serial number must not exceed 255 characters.',
                'serial_no.unique' => 'Serial number must be unique.',
                'name.required' => 'Name is required.',
                'name.string' => 'Name must be a string.',
                'location.required' => 'Location is required.',
                'location.string' => 'Location must be a string.',
                'status.required' => 'Status is required.',
                'status.string' => 'Status must be a string.',
                'procedure.string' => 'Procedure must be a string.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            $asset = Asset::findOrFail($id);

            $asset->update([
                'component_id' => $request->component_id,
                'serial_no' => $request->serial_no,
                'name' => $request->name,
                'location' => $request->location,
                'status' => $request->status,
                'procedure' => $request->procedure,
            ]);

            DB::commit();
            return $this->success('Asset successfully updated.');
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
            $asset = Asset::findOrFail($id);
            $asset->delete();

            DB::commit();
            return $this->success('Asset successfully deleted.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
