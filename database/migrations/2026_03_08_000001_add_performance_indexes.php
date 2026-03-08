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
        // Add indexes to frequently queried columns for better performance

        Schema::table('produks', function (Blueprint $table) {
            // Index for availability filter (common query)
            $table->index('is_available', 'idx_produks_is_available');

            // Index for category and type filters
            $table->index('category', 'idx_produks_category');
            $table->index('type', 'idx_produks_type');

            // Index for price range queries
            $table->index('price', 'idx_produks_price');

            // Composite index for common filters
            $table->index(['is_available', 'category'], 'idx_produks_available_category');

            // Index for ordering by created_at (default sort)
            $table->index('created_at', 'idx_produks_created_at');

            // Index for search by name
            $table->index('name', 'idx_produks_name');
        });

        Schema::table('gambar_produks', function (Blueprint $table) {
            // Index for foreign key lookups
            $table->index('produk_id', 'idx_gambar_produks_produk_id');
        });

        Schema::table('panorama_produks', function (Blueprint $table) {
            // Index for foreign key lookups  
            $table->index('produk_id', 'idx_panorama_produks_produk_id');
        });

        Schema::table('denahs', function (Blueprint $table) {
            // Index for foreign key lookups
            $table->index('produk_id', 'idx_denahs_produk_id');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index for login queries
            $table->index('email', 'idx_users_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropIndex('idx_produks_is_available');
            $table->dropIndex('idx_produks_category');
            $table->dropIndex('idx_produks_type');
            $table->dropIndex('idx_produks_price');
            $table->dropIndex('idx_produks_available_category');
            $table->dropIndex('idx_produks_created_at');
            $table->dropIndex('idx_produks_name');
        });

        Schema::table('gambar_produks', function (Blueprint $table) {
            $table->dropIndex('idx_gambar_produks_produk_id');
        });

        Schema::table('panorama_produks', function (Blueprint $table) {
            $table->dropIndex('idx_panorama_produks_produk_id');
        });

        Schema::table('denahs', function (Blueprint $table) {
            $table->dropIndex('idx_denahs_produk_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
        });
    }
};
