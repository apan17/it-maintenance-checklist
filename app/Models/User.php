<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\QueryableTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasUuids, HasApiTokens, QueryableTrait;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'staff_no',
        'email',
        'is_active',
        'password',
        'profile_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function roles()
    {
        $relation = $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            app(PermissionRegistrar::class)->pivotRole
        )->limit(1);

        if (!app(PermissionRegistrar::class)->teams) {
            return $relation;
        }

        $teamsKey = app(PermissionRegistrar::class)->teamsKey;
        $relation->withPivot($teamsKey);
        $teamField = config('permission.table_names.roles') . '.' . $teamsKey;

        return $relation->wherePivot($teamsKey, getPermissionsTeamId())
            ->where(fn($q) => $q->whereNull($teamField)->orWhere($teamField, getPermissionsTeamId()));
    }
}
