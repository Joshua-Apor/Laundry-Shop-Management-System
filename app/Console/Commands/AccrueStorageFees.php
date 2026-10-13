<?php

namespace App\Console\Commands;

use App\Services\OrderStorageFeeService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('orders:accrue-storage-fees')]
#[Description('Accrue daily storage fees for orders that remain ready for pickup')]
class AccrueStorageFees extends Command
{
    public function handle(OrderStorageFeeService $storageFeeService): int
    {
        $updatedOrders = $storageFeeService->accrueDueFees();

        $this->info("Storage fees updated for {$updatedOrders} order(s).");

        return self::SUCCESS;
    }
}
