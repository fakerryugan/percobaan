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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('ticket_code')->unique();
            $table->string('phone')->nullable();
            $table->string('category')->default('Reguler'); // Reguler, VIP, VVIP, Keluarga, Sahabat
            $table->integer('pax')->default(2); // Maximum guest quota
            $table->string('table_number')->nullable();
            $table->string('rsvp_status')->default('pending'); // pending, attending, declined
            $table->integer('rsvp_pax')->default(0);
            $table->text('wishes')->nullable();
            $table->boolean('is_checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->string('checked_in_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
