<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('pi_number')->unique();
            $table->date('pi_date');
            $table->date('due_date');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'half_yearly', 'yearly']);
            $table->date('usage_period_start');
            $table->date('usage_period_end');
            $table->string('invoice_name');
            $table->text('address')->nullable();
            $table->string('gstin', 20)->nullable();
            $table->string('hsn_sac', 20)->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->enum('tax_type', ['igst', 'cgst_sgst']);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->decimal('grand_total', 12, 2);
            $table->string('amount_in_words', 500)->nullable();
            $table->decimal('balance_due', 12, 2);
            $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'disputed', 'written_off', 'deleted'])
                  ->default('unpaid');
            $table->enum('collection_stage', ['new', 'called', 'promised', 'partial', 'overdue', 'disputed', 'paid'])
                  ->default('new');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pdf_path', 500)->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('imported_at')->nullable();
            $table->date('promise_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['assigned_agent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma_invoices');
    }
};
