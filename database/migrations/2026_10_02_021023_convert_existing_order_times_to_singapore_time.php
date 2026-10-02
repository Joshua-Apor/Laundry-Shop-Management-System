<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->convertOrderTimes('UTC', 'Asia/Singapore');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->convertOrderTimes('Asia/Singapore', 'UTC');
    }

    private function convertOrderTimes(string $sourceTimezone, string $targetTimezone): void
    {
        DB::table('laundry_orders')
            ->whereNotNull('order_time')
            ->orderBy('order_id')
            ->get(['order_id', 'order_date', 'order_time'])
            ->each(function (object $order) use ($sourceTimezone, $targetTimezone): void {
                $sourceDateTime = CarbonImmutable::createFromFormat(
                    '!Y-m-d H:i:s',
                    $order->order_date.' '.$order->order_time,
                    $sourceTimezone,
                );
                $targetDateTime = $sourceDateTime->setTimezone($targetTimezone);

                DB::table('laundry_orders')
                    ->where('order_id', $order->order_id)
                    ->update([
                        'order_date' => $targetDateTime->toDateString(),
                        'order_time' => $targetDateTime->format('H:i:s'),
                    ]);
            });
    }
};
