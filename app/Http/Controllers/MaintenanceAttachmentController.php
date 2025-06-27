<?php
namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\MaintenanceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MaintenanceAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return Maintenance::queryable()->extendPaginate();
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

            DB::commit();
            return $this->success('Maintenance successfully created.');
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
            $maintenance = Maintenance::findOrFail($id);
            return $this->success('Maintenance retrieved successfully.', $maintenance);
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

            DB::commit();
            return $this->success('Maintenance successfully updated.');
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
            $maintenance = MaintenanceAttachment::findOrFail($id);

            $maintenance->attachments()->each(function ($attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            });

            $maintenance->delete();

            DB::commit();
            return $this->success('Maintenance successfully deleted.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
