<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $table = 'laundry_orders';

    protected $primaryKey = 'order_id';

    public $timestamps = false;

    public const STATUSES = [
        'Processing',
        'Ready for Pickup',
        'Completed',
    ];

    public const HISTORY_STATUSES = [
        'Completed',
        'Cancelled',
    ];

    public const ALL_STATUSES = [
        ...self::STATUSES,
        'Cancelled',
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
        $driver = DB::connection()->getDriverName();

        $serviceNamesAggregation = match ($driver) {
            'sqlite' => 'GROUP_CONCAT(DISTINCT services.service_name)',
            'pgsql' => "string_agg(DISTINCT services.service_name, ', ' ORDER BY services.service_name)",
            default => "GROUP_CONCAT(DISTINCT services.service_name ORDER BY services.service_name SEPARATOR ', ')",
        };

        $serviceDetailsAggregation = match ($driver) {
            'sqlite' => "GROUP_CONCAT(services.service_name || ' × ' || order_services.quantity, ', ')",
            'pgsql' => "string_agg(CONCAT(services.service_name, ' × ', order_services.quantity), ', ' ORDER BY services.service_name)",
            default => "GROUP_CONCAT(CONCAT(services.service_name, ' × ', order_services.quantity) ORDER BY services.service_name SEPARATOR ', ')",
        };

        $query
            ->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')
            ->select([
                'laundry_orders.order_id',
                'laundry_orders.customer_id',
                'laundry_orders.user_id',
                'laundry_orders.employee_name as employeeName',
                'laundry_orders.order_date',
                'laundry_orders.order_time',
                'laundry_orders.laundry_weight as weight',
                'laundry_orders.order_type',
                'laundry_orders.self_service_loads',
                'laundry_orders.total_amount',
                'laundry_orders.storage_fee',
                'laundry_orders.ready_for_pickup_at',
                'laundry_orders.amount_paid',
                'laundry_orders.balance',
                'laundry_orders.order_status as status',
                'laundry_orders.pickup_date',
                'customers.name as fullname',
                'customers.contact_number as phoneNumber',
                'customers.email as customerEmail',

            ])
            ->selectSub(
                DB::table('order_services')
                    ->join('services', 'services.service_id', '=', 'order_services.service_id')
                    ->whereColumn('order_services.order_id', 'laundry_orders.order_id')
                    ->selectRaw($serviceNamesAggregation),
                'services',
            )
            ->selectSub(
                DB::table('order_services')
                    ->join('services', 'services.service_id', '=', 'order_services.service_id')
                    ->whereColumn('order_services.order_id', 'laundry_orders.order_id')
                    ->selectRaw($serviceDetailsAggregation),
                'itemDetails',
            )
            ->selectSub(
                DB::table('special_requests')
                    ->whereColumn('special_requests.order_id', 'laundry_orders.order_id')
                    ->orderBy('special_requests.request_id')
                    ->limit(1)
                    ->select('request_details'),
                'specialRequest',
            )
            ->selectSub(
                DB::table('payments')
                    ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                    ->orderBy('payments.payment_date')
                    ->orderBy('payments.payment_id')
                    ->limit(1)
                    ->select('payment_method'),
                'initialPaymentMethod',
            )
            ->selectSub(
                DB::table('payments')
                    ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                    ->orderBy('payments.payment_date')
                    ->orderBy('payments.payment_id')
                    ->limit(1)
                    ->select('reference_number'),
                'initialReferenceNumber',
            )
            ->selectSub(
                DB::table('payments')
                    ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                    ->orderBy('payments.payment_date')
                    ->orderBy('payments.payment_id')
                    ->offset(1)
                    ->limit(1)
                    ->select('payment_id'),
                'balancePaymentId',
            )
            ->selectSub(
                DB::table('payments')
                    ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                    ->orderBy('payments.payment_date')
                    ->orderBy('payments.payment_id')
                    ->offset(1)
                    ->limit(1)
                    ->select('reference_number'),
                'balancePaymentReferenceNumber',
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
            );
    }
}
