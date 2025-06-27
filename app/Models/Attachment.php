<?php

namespace App\Models;

use App\Traits\QueryableTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory, HasUuids, QueryableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $fillable = [
        'name',
        'path',
        'module',
        'description',
        'extension',
        'mime',
        'filesize',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        // 'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
        ];
    }
}