<?php
namespace App\Models;

use App\Traits\QueryableTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory, HasUuids, QueryableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'asset_id',
        'reporter_id',
        'maintainer_id',
        'asset_status',
        'current_status',
        'complete_date',
        'notes',
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

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function maintainer()
    {
        return $this->belongsTo(User::class, 'maintainer_id');
    }
    
    public function maintenanceStatuses()
    {
        return $this->hasMany(MaintenanceStatus::class, 'maintenance_id'); 
    }
}
