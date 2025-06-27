<?php
namespace App\Models;

use App\Traits\QueryableTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, HasUuids, QueryableTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'component_id',
        'serial_no',
        'name',
        'location',
        'status',
        'procedure',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    // ! RELATIONSHIPS
    public function component()
    {
        return $this->belongsTo(Component::class, 'component_id');
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class, 'asset_id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'asset_id');
    }
}
