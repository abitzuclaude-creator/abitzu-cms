<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('line_number');
            $table->text('description');
            $table->string('location_name');
            $table->string('hsn_sac', 20)->nullable();
            $table->string('billing_cycle', 50);
            $table->decimal('price_per_month', 10, 2);
            $table->decimal('discount_per_month', 10, 2)->default(0);
            $table->decimal('net_price_per_month', 10, 2);
            $table->unsignedInteger('billed_days');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('total_with_tax', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_items');
    }
};
