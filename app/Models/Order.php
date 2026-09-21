<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'laundry_orders';

    protected $primaryKey = 'order_id';

    public $timestamps = false;

    public const STATUSES = [
        'Received',
        'Processing',
        'Ready for Pickup',
        'Completed',
    ];

    protected $fillable = [
        'fullname',
        'phoneNumber',
        'service',
        'weight',
        'specialRequest',
        'paymentMethod',
        'amount_paid',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'service' => 'array',
        'weight' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];
}
