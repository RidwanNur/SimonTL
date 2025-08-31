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
        Schema::create('laporan_hasil_pengawasan', function (Blueprint $table) {
            $table->id();
                        // relasi yang benar: user_id -> users.id
            $table->integer('user_id')->nullable();
            $table->string('report_name', 255)->nullable();
            $table->date('report_date')->nullable();
            $table->dateTime('report_dateline')->nullable();
            $table->string('team_lead', 255)->nullable();
            $table->integer('phone_number_teamlead')->nullable(); // mengikuti schema asli
            $table->integer('phone_number_opd')->nullable();      // mengikuti schema asli
            $table->integer('followup_send_status')->nullable();
            $table->string('followup_status', 255)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->string('link_file', 255)->nullable();
            // FK (opsional nullable karena kolom nullable)
            // $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_hasil_pengawasan');
    }
};
