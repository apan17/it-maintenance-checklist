<?php
namespace App\Models;

use App\Traits\QueryableTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory, HasUuids, QueryableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'asset_id',
        'inspector_id',
        'asset_status',
        'current_asset_status',
        'due_date',
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

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
