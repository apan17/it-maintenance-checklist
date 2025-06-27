<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return User::queryable()->extendPaginate();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {DB::beginTransaction();
        try {
            $rules = [
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'username' => 'required|string|max:255',
                'fullname' => 'required|string|max:255',
                'contact_no' => 'required|string|max:255',
            ];

            $message = [
                'email.required' => 'E-mel diperlukan.',
                'email.email' => 'E-mel tidak sah.',
                'email.unique' => 'E-mel telah didaftarkan.',
                'password.required' => 'Kata laluan diperlukan.',
                'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                'username.required' => 'Nama akaun diperlukan.',
                'fullname.required' => 'Nama penuh diperlukan.',
                'contact_no.required' => 'No. telefon diperlukan.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            // get the last staff_no
            $lastUser = User::orderBy('staff_no', 'desc')->first();

            $latestStaffNo = $lastUser?->staff_no ? str_pad((int) $lastUser?->staff_no + 1, 5, '0', STR_PAD_LEFT) : '00001';

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'staff_no' => $latestStaffNo,
            ])->assignRole('staff')
                ->profile()->create([
                'username' => $request->username,
                'fullname' => $request->fullname,
                'contact_no' => $request->contact_no,
            ]);

            DB::commit();
            return $this->success('Pengguna berjaya didaftarkan.');
        } catch (\Exception $th) {
            DB::rollBack();
            throw $th;
        }}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deactivateUser(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update(['is_active' => request()->is_active]);
            DB::commit();
            return $this->success('Pengguna berjaya dinyahaktifkan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
