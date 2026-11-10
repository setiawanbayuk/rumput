<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('esuket_push_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('device_id', 120);
            $table->enum('platform', ['android', 'ios']);
            // Token asli dienkripsi otomatis dengan cast model, tidak disimpan plaintext.
            $table->text('fcm_token');
            $table->char('token_hash', 64)->unique();
            $table->timestamps();
            $table->unique(['user_id', 'device_id']);
            $table->index(['user_id', 'platform']);
        });

        Schema::create('esuket_push_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_pengajuan_id')->constrained('surat_pengajuans')->cascadeOnDelete();
            $table->foreignId('push_device_id')->constrained('esuket_push_devices')->cascadeOnDelete();
            $table->string('event_name', 24);
            $table->string('status', 16)->default('pending');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->string('error_code', 120)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['surat_pengajuan_id', 'push_device_id', 'event_name'], 'esuket_delivery_once');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esuket_push_deliveries');
        Schema::dropIfExists('esuket_push_devices');
    }
};
