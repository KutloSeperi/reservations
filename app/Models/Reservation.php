<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id', 'customer_name', 'customer_email',
        'start_at', 'end_at', 'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Active reservations that overlap the given period.
     * End-equals-start is NOT considered overlapping, per brief.
     */
    public function scopeOverlapping(Builder $query, int $resourceId, $startAt, $endAt, ?int $excludeId = null): Builder
    {
        return $query
            ->where('resource_id', $resourceId)
            ->where('status', 'active')
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId));
    }
}