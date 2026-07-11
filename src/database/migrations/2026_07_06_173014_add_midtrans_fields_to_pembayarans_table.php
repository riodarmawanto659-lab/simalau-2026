<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table): void {
            if (! Schema::hasColumn('pembayarans', 'midtrans_order_id')) {
                $table->string('midtrans_order_id')->nullable()->unique()->after('nomor_pembayaran');
            }

            if (! Schema::hasColumn('pembayarans', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('midtrans_order_id');
            }

            if (! Schema::hasColumn('pembayarans', 'snap_redirect_url')) {
                $table->text('snap_redirect_url')->nullable()->after('snap_token');
            }

            if (! Schema::hasColumn('pembayarans', 'transaction_status')) {
                $table->string('transaction_status')->nullable()->after('status_pembayaran');
            }

            if (! Schema::hasColumn('pembayarans', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('transaction_status');
            }

            if (! Schema::hasColumn('pembayarans', 'fraud_status')) {
                $table->string('fraud_status')->nullable()->after('payment_type');
            }

            if (! Schema::hasColumn('pembayarans', 'midtrans_response')) {
                $table->json('midtrans_response')->nullable()->after('catatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table): void {
            foreach ([
                'midtrans_order_id',
                'snap_token',
                'snap_redirect_url',
                'transaction_status',
                'payment_type',
                'fraud_status',
                'midtrans_response',
            ] as $column) {
                if (Schema::hasColumn('pembayarans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};