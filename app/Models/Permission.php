<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Traits\QueryableTrait;

class Permission extends SpatiePermission
{
    use HasFactory;
    use HasUuids;
    use QueryableTrait;
}
