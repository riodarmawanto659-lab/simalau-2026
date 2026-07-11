<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayarans', function (Blueprint $table): void {
            if (! Schema::hasColumn('pembayarans', 'snap_redirect_url')) {
                $table->text('snap_redirect_url')->nullable()->after('snap_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table): void {
            if (Schema::hasColumn('pembayarans', 'snap_redirect_url')) {
                $table->dropColumn('snap_redirect_url');
            }
        });
    }
};
