<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePayments extends Command
{
    protected $signature = 'midtrans:expire-payments';
    protected $description = 'Expire payments that have passed their expiry time';

    public function handle(): int
    {
        $expired = Pembayaran::where('status_pembayaran', 'menunggu_pembayaran')
            ->where('expired_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($expired as $pembayaran) {
            DB::transaction(function () use ($pembayaran) {
                $pembayaran->update(['status_pembayaran' => 'kadaluwarsa']);
                $pembayaran->orderan->update(['status_orderan' => 'kadaluwarsa']);
            });
            $count++;
        }

        $this->info("Expired {$count} payment(s).");

        return Command::SUCCESS;
    }
}
