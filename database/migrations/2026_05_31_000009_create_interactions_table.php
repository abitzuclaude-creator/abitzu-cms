<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proforma_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->enum('type', ['phone_call', 'whatsapp', 'note']);
            $table->dateTime('interaction_date');
            $table->string('duration', 20)->nullable();
            $table->enum('disposition', ['reached', 'not_reached', 'voicemail', 'busy', 'wrong_number', 'disconnected'])->nullable();
            $table->enum('whatsapp_template', ['pre_due', 'post_due'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
