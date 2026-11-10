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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email')->index();
            $table->string('phone_number')->index();
            $table->string('institution'); // Instansi (wajihah)
            $table->string('position'); // Jabatan (amanah)
            $table->enum('attendance_status', ['hadir', 'tidak_hadir', 'hadir_terlambat'])->default('hadir');
            $table->string('qr_code_token', 64)->unique()->index();
            $table->boolean('is_checked_in')->default(false)->index();
            $table->dateTime('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('checkin_method', 20)->nullable(); // 'qr_scan' or 'manual'
            $table->boolean('email_sent')->default(false);
            $table->dateTime('email_sent_at')->nullable();
            $table->string('mailketing_message_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->dateTime('attended_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Compound index for anti-duplication query efficiency
            $table->index(['event_id', 'email']);
            $table->index(['event_id', 'phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
