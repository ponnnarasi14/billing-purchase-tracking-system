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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 14, 2); 
            $table->decimal('total_tax', 14, 2);
            $table->decimal('grand_total', 14, 2);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('balance_returned', 14, 2)->default(0);
            $table->json('balance_breakdown')->nullable();
            $table->timestamps();
            
            $table->index(['customer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
