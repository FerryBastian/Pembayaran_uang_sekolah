<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;

class NotificationService
{
    public function sendToUser(?int $userId, string $judul, string $pesan): void
    {
        if (! $userId) {
            return;
        }

        Notifikasi::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'status' => 'belum_dibaca',
        ]);
    }

    public function sendToAdmins(string $judul, string $pesan): void
    {
        User::query()
            ->where('role', 'admin')
            ->pluck('id')
            ->each(fn (int $userId) => $this->sendToUser($userId, $judul, $pesan));
    }
}
