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
        Schema::create('rental_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('occupancy_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('previous_agreement_id')
                ->nullable()
                ->constrained('rental_agreements')
                ->nullOnDelete();

            $table->string('agreement_no')->unique();

            $table->date('agreement_start_date');

            $table->date('agreement_end_date');

            $table->decimal('monthly_rent', 12, 2)
                ->nullable();

            $table->decimal('security_deposit', 12, 2)
                ->nullable();

            $table->enum('status', [
                'active',
                'expired',
                'terminated',
                'renewed',
            ]);

            $table->string('agreement_file')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_agreements');
    }
};
