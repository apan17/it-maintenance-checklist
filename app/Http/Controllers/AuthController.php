<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        DB::beginTransaction();
        try {
            $credentials = $request->only('email', 'password');

            $logged = Auth::attempt($credentials);
            abort_if(!$logged, 401, 'Sila masukkan E-mel yang didaftarkan & Kata laluan yang sah.');

            DB::commit();
            return $this->success('Berjaya', new UserResource(Auth::user()));
        } catch (\Exception $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function logout(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $user = $request->user();
        $user->tokens()->delete();

        return $this->success('Berjaya Log Keluar');
    }

    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'staff_no' => 'required|string|min:5|unique:users,staff_no',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'username' => 'required|string|max:255',
                'fullname' => 'required|string|max:255',
            ];

            $message = [
                'staff_no.required' => 'No. staff diperlukan.',
                'staff_no.string' => 'No. staff must be a string.',
                'staff_no.min' => 'No. staff mesti sekurang-kurangnya 5 aksara.',
                'staff_no.unique' => 'No. staff telah didaftarkan.',
                'email.required' => 'E-mel diperlukan.',
                'email.email' => 'E-mel tidak sah.',
                'email.unique' => 'E-mel telah didaftarkan.',
                'password.required' => 'Kata laluan diperlukan.',
                'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                'password.confirmed' => 'Kata laluan tidak sepadan.',
                'username.required' => 'Nama akaun diperlukan.',
                'fullname.required' => 'Nama penuh diperlukan.',
            ];

            $validator = Validator::make($request->all(), $rules);

            abort_if($validator->fails(), 422, $validator->errors()->first());

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'staff_no' =>  $request->staff_no,
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
        }
    }

    public function getRole(){
        try {
            $roles = Role::whereNotIn('name', ['masteradmin'])
                ->orderBy('name')
                ->get(['id', 'label']);
            return $this->success('Senarai peranan berjaya diperoleh.', $roles);
        } catch (\Exception $th) {
            throw $th;
        }
    }
}
