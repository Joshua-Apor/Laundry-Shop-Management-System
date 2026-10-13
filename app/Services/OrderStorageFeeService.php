<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class OrderStorageFeeService
{
    private const DAILY_STORAGE_FEE = 20;

    private const SECONDS_PER_DAY = 86400;

    public function accrueDueFees(): int
    {
        $updatedOrders = 0;

        DB::table('laundry_orders')
            ->where('order_status', 'Ready for Pickup')
            ->whereNotNull('ready_for_pickup_at')
            ->orderBy('order_id')
            ->chunkById(100, function (Collection $orders) use (&$updatedOrders): void {
                foreach ($orders as $order) {
                    if ($this->accrueForOrder((int) $order->order_id)) {
                        $updatedOrders++;
                    }
                }
            }, 'order_id');

        return $updatedOrders;
    }

    public function accrueForOrder(int $orderId): bool
    {
        return DB::transaction(function () use ($orderId): bool {
            $order = DB::table('laundry_orders')
                ->where('order_id', $orderId)
                ->lockForUpdate()
                ->first([
                    'order_id',
                    'order_status',
                    'ready_for_pickup_at',
                    'storage_fee',
                    'total_amount',
                    'balance',
                ]);

            if ($order === null || $order->order_status !== 'Ready for Pickup' || $order->ready_for_pickup_at === null) {
                return false;
            }

            return $this->applyDueFee($order);
        });
    }

    private function applyDueFee(stdClass $order): bool
    {
        $readyAt = Carbon::parse($order->ready_for_pickup_at);
        $elapsedSeconds = max(0, now()->getTimestamp() - $readyAt->getTimestamp());
        $feeDays = $elapsedSeconds < (2 * self::SECONDS_PER_DAY)
            ? 0
            : intdiv($elapsedSeconds, self::SECONDS_PER_DAY) - 1;
        $targetStorageFee = $feeDays * self::DAILY_STORAGE_FEE;
        $feeToAdd = round($targetStorageFee - (float) $order->storage_fee, 2);

        if ($feeToAdd <= 0) {
            return false;
        }

        DB::table('laundry_orders')
            ->where('order_id', $order->order_id)
            ->update([
                'storage_fee' => $targetStorageFee,
                'total_amount' => round((float) $order->total_amount + $feeToAdd, 2),
                'balance' => round((float) $order->balance + $feeToAdd, 2),
            ]);

        return true;
    }
}
