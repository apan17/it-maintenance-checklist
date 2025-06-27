<?php
namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\ChecklistAttachment;
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
            return ChecklistAttachment::queryable()->extendPaginate();
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
            return $this->success('Checklist successfully created.');
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
            $checklist = ChecklistAttachment::findOrFail($id);
            return $this->success('Checklist retrieved successfully.', $checklist);
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
            return $this->success('Checklist successfully updated.');
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
            $checklist = ChecklistAttachment::findOrFail($id);

            $checklist->attachments()->each(function ($attachment) {
                Storage::disk('public')->delete($attachment->path);
                $attachment->delete();
            });

            $checklist->delete();

            DB::commit();
            return $this->success('Checklist successfully deleted.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
