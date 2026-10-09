<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week')->unique(); // 0 = Sunday … 6 = Saturday (matches PHP's date('w'))
            $table->boolean('is_closed')->default(false);
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            // For a night that runs past midnight (e.g. opens 18:00, closes 01:00) closes_at
            // is stored as-is (01:00) and 'closes_next_day' marks that it rolls into the next date.
            $table->boolean('closes_next_day')->default(false);
            $table->timestamps();
        });

        // Seed all 7 days with sensible defaults (open 11:00–22:00) so the admin page
        // and the booking form always have a full week to show, never a gap.
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = [
                'day_of_week'      => $i,
                'is_closed'        => false,
                'opens_at'         => '11:00:00',
                'closes_at'        => '22:00:00',
                'closes_next_day'  => false,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }
        DB::table('business_hours')->insert($days);
    }

    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};
