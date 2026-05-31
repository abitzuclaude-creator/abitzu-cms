<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->index();
            $table->string('business_id', 100)->unique();
            $table->enum('invoice_type', ['ho_level', 'branch_level']);
            $table->string('invoice_name');
            $table->text('address')->nullable();
            $table->string('gstin', 20)->nullable()->index();
            $table->enum('gstin_status', ['valid', 'awaiting_filing'])->default('valid');
            $table->string('pan', 15)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('owner_name')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('owner_phone1', 20)->nullable();
            $table->string('owner_phone2', 20)->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->foreignId('deactivated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
