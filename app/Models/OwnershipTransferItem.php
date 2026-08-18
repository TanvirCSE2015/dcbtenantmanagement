<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OwnershipTransferItem extends Model
{
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(
            OwnershipTransfer::class,
            'ownership_transfer_id'
        );
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
