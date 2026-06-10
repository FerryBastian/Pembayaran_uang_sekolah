<?php

namespace App\Jobs;

use App\Models\OrangTua;
use App\Models\TagihanSiswa;
use App\Services\WhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWhatsappPengingat implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public OrangTua $orangTua,
        public TagihanSiswa $tagihanSiswa,
    ) {}

    public function handle(WhatsappService $whatsappService): void
    {
        $whatsappService->sendPengingatTagihan($this->orangTua, $this->tagihanSiswa);
    }
}
