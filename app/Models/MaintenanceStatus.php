<?php
namespace App\Models;

use App\Traits\QueryableTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceStatus extends Model
{
    use HasFactory, HasUuids, QueryableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'maintenance_id',
        'status',
        'notes',
        'date',
        'is_current',
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
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id'); 
    }
}
