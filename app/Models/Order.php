<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $hidden = ['created_at', 'updated_at', 'deleted_at']; // Menyembunyikan created_at dan updated_at secara global
    protected $fillable =
    [
        'customer_name',
        'table_no',
        'order_date',
        'order_time',
        'status',
        'total',
        'user_id'
    ];

    public function sumOrderPrice()
    {
        $orderDetail = OrderDetail::where('order_id', $this->id)->pluck('price');
        $sum    = collect($orderDetail)->sum();

        return $sum;
    }

    /**
     * Get all of the comments for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetail(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    /**
     * Get the user that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
