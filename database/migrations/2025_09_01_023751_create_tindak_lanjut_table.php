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
        Schema::create('tindak_lanjut', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('report_name', 255)->nullable();
            $table->date('report_date')->nullable();
            $table->dateTime('report_dateline')->nullable();
            $table->string('team_lead', 255)->nullable();
            $table->integer('phone_number_opd')->nullable();      
            $table->integer('followup_send_status')->nullable();
            $table->string('followup_status', 255)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->string('bukti_upload', 255)->nullable();
            $table->integer('is_deleted')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut');
    }
};
