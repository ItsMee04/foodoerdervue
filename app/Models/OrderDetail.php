<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $hidden = ['created_at', 'updated_at', 'deleted_at']; // Menyembunyikan created_at dan updated_at secara global
    protected $fillable = [
        'order_id',
        'item_id',
        'price'
    ];

    /**
     * Get the user that owns the OrderDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
