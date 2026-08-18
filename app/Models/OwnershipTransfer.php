<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OwnershipTransfer extends Model
{
    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function ownable(): MorphTo
    {
        return $this->morphTo();
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            OwnershipTransferItem::class
        );
    }

    public function fromOwners(): HasMany
    {
        return $this->hasMany(
            OwnershipTransferItem::class
        )->where('direction', 'from');
    }

    public function toOwners(): HasMany
    {
        return $this->hasMany(
            OwnershipTransferItem::class
        )->where('direction', 'to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
