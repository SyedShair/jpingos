<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_pre_order')->default(false)->after('order_type');
            $table->date('pre_order_date')->nullable()->after('is_pre_order');
            $table->time('pre_order_time')->nullable()->after('pre_order_date');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_pre_order', 'pre_order_date', 'pre_order_time']);
        });
    }
};