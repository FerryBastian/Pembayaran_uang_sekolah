<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsappPembayaranBerhasil;
use App\Models\Pembayaran;
use App\Models\TagihanSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MidtransCallbackTest extends TestCase
{
    use RefreshDatabase;

    private const SERVER_KEY = 'midtrans-test-server-key';

    public function test_it_rejects_an_invalid_signature(): void
    {
        [$pembayaran] = $this->createPaymentRecords();

        $response = $this->postJson('/api/midtrans/callback', $this->callbackPayload(
            orderId: $pembayaran->order_id,
            transactionStatus: 'settlement',
            signature: 'invalid-signature',
        ));

        $response->assertForbidden();
        $this->assertSame('pending', $pembayaran->fresh()->transaction_status);
    }

    public function test_it_rejects_an_incomplete_callback_payload(): void
    {
        $response = $this->postJson('/api/midtrans/callback', [
            'order_id' => 'SPP-INCOMPLETE',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'status_code',
            'gross_amount',
            'signature_key',
            'transaction_status',
        ]);
    }

    public function test_it_marks_a_valid_settlement_as_paid_and_dispatches_whatsapp(): void
    {
        Queue::fake();
        [$pembayaran, $tagihanSiswa] = $this->createPaymentRecords();
        $payload = $this->callbackPayload(
            orderId: $pembayaran->order_id,
            transactionStatus: 'settlement',
        );

        $response = $this->postJson('/api/midtrans/callback', $payload);

        $response->assertOk();
        $this->assertSame('lunas', $pembayaran->fresh()->transaction_status);
        $this->assertSame('lunas', $tagihanSiswa->fresh()->status);
        Queue::assertPushed(SendWhatsappPembayaranBerhasil::class, 1);
    }

    public function test_it_does_not_downgrade_a_paid_transaction(): void
    {
        Queue::fake();
        [$pembayaran, $tagihanSiswa] = $this->createPaymentRecords('lunas');
        $payload = $this->callbackPayload(
            orderId: $pembayaran->order_id,
            transactionStatus: 'deny',
            statusCode: '202',
        );

        $response = $this->postJson('/api/midtrans/callback', $payload);

        $response->assertOk();
        $this->assertSame('lunas', $pembayaran->fresh()->transaction_status);
        $this->assertSame('lunas', $tagihanSiswa->fresh()->status);
        Queue::assertNothingPushed();
    }

    protected function setUp(): void
    {
        parent::setUp();

        config(['midtrans.server_key' => self::SERVER_KEY]);
    }

    private function callbackPayload(
        string $orderId,
        string $transactionStatus,
        string $statusCode = '200',
        ?string $signature = null,
    ): array {
        $grossAmount = '10000.00';

        return [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature ?? hash(
                'sha512',
                $orderId.$statusCode.$grossAmount.self::SERVER_KEY
            ),
            'transaction_status' => $transactionStatus,
            'fraud_status' => 'accept',
            'payment_type' => 'credit_card',
        ];
    }

    private function createPaymentRecords(string $status = 'pending'): array
    {
        $now = now();
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin',
            'username' => 'admin-test',
            'email' => 'admin-test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $parentUserId = DB::table('users')->insertGetId([
            'name' => 'Orang Tua',
            'username' => 'parent-test',
            'email' => 'parent-test@example.com',
            'password' => bcrypt('password'),
            'role' => 'orang_tua',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $studentUserId = DB::table('users')->insertGetId([
            'name' => 'Siswa',
            'username' => 'student-test',
            'email' => 'student-test@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $kelasId = DB::table('kelas')->insertGetId([
            'nama_kelas' => 'X IPA 1',
            'wali_kelas' => 'Wali Kelas',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $orangTuaId = DB::table('orang_tuas')->insertGetId([
            'user_id' => $parentUserId,
            'nama' => 'Orang Tua',
            'no_hp' => '081234567890',
            'no_wa' => '081234567890',
            'alamat' => 'Alamat',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $siswaId = DB::table('siswas')->insertGetId([
            'user_id' => $studentUserId,
            'kelas_id' => $kelasId,
            'orang_tua_id' => $orangTuaId,
            'nisn' => '1234567890',
            'nama' => 'Siswa',
            'alamat' => 'Alamat',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2010-01-01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $tagihanId = DB::table('tagihans')->insertGetId([
            'created_by' => $adminId,
            'judul' => 'SPP Juni',
            'nominal' => 10000,
            'bulan' => 6,
            'tahun' => 2026,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $tagihanSiswaId = DB::table('tagihan_siswas')->insertGetId([
            'tagihan_id' => $tagihanId,
            'siswa_id' => $siswaId,
            'status' => $status,
            'jatuh_tempo' => '2026-06-30',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $pembayaranId = DB::table('pembayarans')->insertGetId([
            'tagihan_siswa_id' => $tagihanSiswaId,
            'order_id' => 'SPP-TEST-'.uniqid(),
            'gross_amount' => 10000,
            'payment_type' => 'midtrans_snap',
            'transaction_status' => $status,
            'snap_token' => 'test-token',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return [
            Pembayaran::findOrFail($pembayaranId),
            TagihanSiswa::findOrFail($tagihanSiswaId),
        ];
    }
}
