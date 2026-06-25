<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayarans', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran')->nullable()->after('transaction_time');
            }

            if (! Schema::hasColumn('pembayarans', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')->nullable()->after('bukti_pembayaran');
            }

            if (! Schema::hasColumn('pembayarans', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('catatan_verifikasi')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('pembayarans', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }

            if (Schema::hasColumn('pembayarans', 'snap_token')) {
                $table->dropColumn('snap_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayarans', 'snap_token')) {
                $table->text('snap_token')->nullable()->after('transaction_time');
            }

            if (Schema::hasColumn('pembayarans', 'bukti_pembayaran')) {
                $table->dropColumn('bukti_pembayaran');
            }

            if (Schema::hasColumn('pembayarans', 'catatan_verifikasi')) {
                $table->dropColumn('catatan_verifikasi');
            }

            if (Schema::hasColumn('pembayarans', 'verified_by')) {
                $table->dropConstrainedForeignId('verified_by');
            }

            if (Schema::hasColumn('pembayarans', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
        });
    }
};
