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
                'username' => 'required|string|max:255',
                'fullname' => 'required|string|max:255',
            ];

            $message = [
                'email.required' => 'E-mel diperlukan.',
                'email.email' => 'E-mel tidak sah.',
                'email.unique' => 'E-mel telah didaftarkan.',
                'username.required' => 'Nama akaun diperlukan.',
                'fullname.required' => 'Nama penuh diperlukan.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make('password'),
                'staff_no' => $request->staff_no,
            ])->assignRole('staff')
                ->profile()->create([
                'username' => $request->username,
                'fullname' => $request->fullname,
                'contact_no' => $request->contact_no ?? null,
            ]);

            DB::commit();
            return $this->success('User successfully created.');
        } catch (\Exception $th) {
            DB::rollBack();
            throw $th;
        }}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return $this->success('User successfully retrieved.', $user);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(User $user, Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'password' => 'nullable|min:8|confirmed',
                'username' => 'required|string|max:255',
                'fullname' => 'required|string|max:255',
            ];

            $message = [
                'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                'password.confirmed' => 'Kata laluan tidak sepadan.',
                'username.required' => 'Nama akaun diperlukan.',
                'fullname.required' => 'Nama penuh diperlukan.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            if($request->password) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $user->profile()->update([
                'username' => $request->username,
                'fullname' => $request->fullname,
                'contact_no' => $request->contact_no ?? null,
            ]);

            DB::commit();
            return $this->success('User successfully updated.');
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
        //
    }

    public function deactivateUser(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->update(['is_active' => $request->is_active]);
            DB::commit();
            return $this->success('Pengguna berjaya dinyahaktifkan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
