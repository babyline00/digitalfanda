<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'product_file_id',
        'token',
        'expires_at',
        'max_downloads',
        'download_count',
        'last_downloaded_at',
        'last_ip',
    ];

    protected $casts = [
        'max_downloads' => 'integer',
        'download_count' => 'integer',
        'expires_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productFile(): BelongsTo
    {
        return $this->belongsTo(ProductFile::class);
    }

    public function isValid(): bool
    {
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_downloads && $this->download_count >= $this->max_downloads) {
            return false;
        }
        return true;
    }

    public function incrementDownload(string $ip): void
    {
        $this->increment('download_count');
        $this->update([
            'last_downloaded_at' => now(),
            'last_ip' => $ip,
        ]);
    }
}