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
        Schema::table('produks', function (Blueprint $table) {
            if (!Schema::hasColumn('produks', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('produks', 'price')) {
                $table->decimal('price', 15, 2);
            }
            if (!Schema::hasColumn('produks', 'category')) {
                $table->enum('category', ['properti', 'rumah'])->default('properti');
            }
            if (!Schema::hasColumn('produks', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('produks', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('produks', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('produks', 'model_3d')) {
                $table->string('model_3d')->nullable();
            }
            if (!Schema::hasColumn('produks', 'is_available')) {
                $table->boolean('is_available')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'price',
                'category',
                'description',
                'image',
                'model_3d',
                'is_available',
            ]);
        });
    }
};
