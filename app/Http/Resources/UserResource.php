<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->resource->tokens()->delete();
        $token = $this->resource->createToken(config('app.name'), ['*'], now()->addDay());
        abort_if(!$token, 422, 'Ralat sistem.');

        $user = $this->resource;
        $roles = $this->resource->roles->first()->label;
        $permissions = $this->resource->getAllPermissions()->pluck('name');

        $response = [
            'user' => [
                "id" => $user->id,
                "staff_no" => $user->staff_no,
                "email" => $user->email,
                "username" => $user->profile->username,
                "fullname" => $user->profile->fullname,
                "contact_no" => $user->profile->contact_no,
            ],
            'roles' => $roles,
            'permissions' => $permissions,
            'token' => $token->plainTextToken,
            'isAuthenticated' => true,
        ];

        return $response;
    }
}
