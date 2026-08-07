<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'status', 'capacity'];

    protected function casts(): array
    {
        return ['capacity' => 'integer'];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}