<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $table = 'laundry_orders';

    protected $primaryKey = 'order_id';

    public $timestamps = false;

    public const STATUSES = [
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

    #[Scope]
    protected function withRecordDetails(Builder $query): void
    {
        $query
            ->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')
            ->leftJoin('users', 'users.user_id', '=', 'laundry_orders.user_id')
            ->select([
                'laundry_orders.order_id',
                'laundry_orders.customer_id',
                'laundry_orders.user_id',
                'laundry_orders.order_date',
                'laundry_orders.order_time',
                'laundry_orders.laundry_weight as weight',
                'laundry_orders.total_amount',
                'laundry_orders.amount_paid',
                'laundry_orders.balance',
                'laundry_orders.order_status as status',
                'laundry_orders.pickup_date',
                'customers.name as fullname',
                'customers.contact_number as phoneNumber',
                'customers.address as customerAddress',
                'users.name as employeeName',
            ])
            ->selectSub(
                DB::table('order_services')
                    ->join('services', 'services.service_id', '=', 'order_services.service_id')
                    ->whereColumn('order_services.order_id', 'laundry_orders.order_id')
                    ->selectRaw("GROUP_CONCAT(DISTINCT services.service_name ORDER BY services.service_name SEPARATOR ', ')"),
                'services',
            )
            ->selectSub(
                DB::table('payments')
                    ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                    ->orderByDesc('payments.payment_date')
                    ->orderByDesc('payments.payment_id')
                    ->limit(1)
                    ->select('payment_method'),
                'paymentMethod',
            )
            ->selectSub(
                DB::table('payments')
                    ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                    ->orderByDesc('payments.payment_date')
                    ->orderByDesc('payments.payment_id')
                    ->limit(1)
                    ->select('payment_status'),
                'paymentStatus',
            )
            ->selectSub(
                DB::table('payments')
                    ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                    ->orderByDesc('payments.payment_date')
                    ->orderByDesc('payments.payment_id')
                    ->limit(1)
                    ->select('payment_date'),
                'paymentDate',
            )
            ->selectSub(
                DB::table('payments')
                    ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                    ->orderByDesc('payments.payment_date')
                    ->orderByDesc('payments.payment_id')
                    ->limit(1)
                    ->select('reference_number'),
                'referenceNumber',
            );
    }
}
