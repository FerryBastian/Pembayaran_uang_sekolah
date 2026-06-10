<?php

namespace App\Jobs;

use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Services\WhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWhatsappTagihanBaru implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public OrangTua $orangTua,
        public Tagihan $tagihan,
        public Siswa $siswa,
    ) {}

    public function handle(WhatsappService $whatsappService): void
    {
        $whatsappService->sendTagihanBaru($this->orangTua, $this->tagihan, $this->siswa);
    }
}
