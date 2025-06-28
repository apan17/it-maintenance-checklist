<?php
namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Checklist;
use App\Models\Maintenance;
use App\Models\ChecklistAttachment;
use App\Models\MaintenanceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return Checklist::queryable()->extendPaginate();
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
            $checklist = Checklist::findOrFail($id);
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
            $rules = [
                'attachments'   => 'nullable|array',
                'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:2048',

                'asset_status'  => 'required|string',
                'notes'         => 'nullable|string',
            ];

            $message = [
                'asset_status.required'  => 'Asset status is required.',
                'asset_status.string'    => 'Asset status must be a string.',

                'notes.string'           => 'Notes must be a string.',

                'attachments.array'      => 'Attachments must be an array.',
                'attachments.*.file'     => 'Each attachment must be a file.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            $inspector = auth()->user();
            $checklist = Checklist::findOrFail($id);

            $checklist->update([
                'inspector_id'         => $inspector->id,
                'current_asset_status' => $request->asset_status,
                'complete_date'        => \Carbon\Carbon::now(),
                'notes'                => $request->notes,
            ]);

            $checklist->asset->update([
                'status' => $request->asset_status,
            ]);

            if ($request->hasFile('attachments')) {
                $attachments = $request->file('attachments');
                foreach ($attachments as $file) {
                    // $fileName = $file->getClientOriginalName();
                    $fileName  = urldecode($file->getClientOriginalName());
                    $extension = $file->getClientOriginalExtension(); // Only the extension

                    $timestamp = now()->timestamp; // Current timestamp

                    Storage::disk("public")->makeDirectory('attachment/checklist');

                    if (! $file->isValid()) {
                        throw new \Exception("Invalid file upload.");
                    }

                    $path = Storage::disk('public')->putFileAs('attachment/checklist', $file, "{$timestamp}.{$extension}");

                    // Debug jika gagal
                    if (! $path) {
                        throw new \Exception("Failed to store file.");
                    }

                    // get mime type && get file size
                    $mimeType = $file->getClientMimeType();
                    $fileSize = $file->getSize();

                    abort_if(! $path, 422, 'Gagal muat naik dokumen');

                    // Store file metadata in the Attachment model
                    $attachment = Attachment::create([
                        'name'        => $fileName,
                        'path'        => $path, // Use the correct file path for S3
                        'module'      => 'CHECKLIST',
                        'description' => $request->description,
                        'extension'   => $extension,
                        'mime'        => $mimeType,
                        'filesize'    => $fileSize,
                    ]);

                    ChecklistAttachment::create([
                        'checklist_id' => $checklist->id,
                        'attachment_id'  => $attachment->id,
                    ]);
                }
            }

            if ($request->asset_status == 'ABNORMAL') {
                $maintenance = Maintenance::create([
                    'asset_id'       => $checklist->asset_id,
                    'reporter_id'    => $inspector->id,
                    'asset_status'   => $request->asset_status,
                    'current_status' => 'PENDING',
                ]);

                MaintenanceStatus::create([
                    'maintenance_id' => $maintenance->id,
                    'status'         => 'PENDING',
                    'asset_status'   => $request->asset_status,
                    'is_current'     => true,
                    'date'           => now(),
                ]);
            }

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
            $checklist = Checklist::findOrFail($id);
            $checklist->delete();

            DB::commit();
            return $this->success('Checklist successfully deleted.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
