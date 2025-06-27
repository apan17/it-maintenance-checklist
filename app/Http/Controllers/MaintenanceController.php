<?php
namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\MaintenanceAttachment;
use App\Models\MaintenanceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class MaintenanceController extends Controller
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
            $rules = [
                'maintenance_id' => 'required|exists:maintenance,id',

                'attachments'    => 'nullable|array',
                'attachments.*'  => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:2048',

                'asset_status'   => 'required|string',
                'status'         => 'required|string',

                'notes'          => 'nullable|string',
            ];

            $message = [
                'asset_id.required'     => 'Asset is required.',
                'asset_id.exists'       => 'Asset does not exist.',
                'asset_status.required' => 'Asset status is required.',
                'asset_status.string'   => 'Asset status must be a string.',

                'status.required'       => 'Status is required.',
                'status.string'         => 'Status must be a string.',

                'notes.string'          => 'Notes must be a string.',

                'attachments.array'     => 'Attachments must be an array.',
                'attachments.*.file'    => 'Each attachment must be a file.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            $maintainer  = auth()->user();
            $maintenance = Maintenance::findOrFail($id);

            $maintenance->update([
                'asset_id'       => $request->asset_id,
                'maintainer_id'  => $maintainer->id,
                'asset_status'   => $request->asset_status,
                'current_status' => $request->status,
                'notes'          => $request->notes,
            ]);

            $maintenance->asset->update([
                'status' => $request->asset_status,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($attachments as $file) {
                    // $fileName = $file->getClientOriginalName();
                    $fileName  = urldecode($file->getClientOriginalName());
                    $extension = $file->getClientOriginalExtension(); // Only the extension

                    $timestamp = now()->timestamp; // Current timestamp

                    Storage::disk("public")->makeDirectory('attachment/maintenance');

                    if (! $file->isValid()) {
                        throw new \Exception("Invalid file upload.");
                    }

                    $path = Storage::disk('public')->putFileAs('attachment/maintenance', $file, "{$timestamp}.{$extension}");

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
                        'module'      => 'MAINTENANCE',
                        'description' => $request->description,
                        'extension'   => $extension,
                        'mime'        => $mimeType,
                        'filesize'    => $fileSize,
                    ]);

                    MaintenanceAttachment::create([
                        'maintenance_id' => $maintenance->id,
                        'attachment_id'  => $attachment->id,
                    ]);
                }
            }

            MaintenanceStatus::create([
                'maintenance_id' => $maintenance->id,
                'status'         => $request->status,
                'notes'         => $request->notes,
                'is_current'     => true,
                'date'           => now(),
            ]);

            if ($request->status == 'Completed') {
                $maintenance->complete_date = now();
                $maintenance->save();
            }

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
            $maintenance = Maintenance::findOrFail($id);
            $maintenance->delete();

            DB::commit();
            return $this->success('Maintenance successfully deleted.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
