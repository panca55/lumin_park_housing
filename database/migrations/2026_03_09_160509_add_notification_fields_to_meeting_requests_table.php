<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meeting_requests', function (Blueprint $table) {
            $table->timestamp('notification_read_at')->nullable()->after('notified_if_sold');
            $table->json('sold_product_ids')->nullable()->after('notification_read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_requests', function (Blueprint $table) {
            $table->dropColumn(['notification_read_at', 'sold_product_ids']);
        });
    }
};
