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
        Schema::create('tenant_arms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_agreement_id')
                ->constrained()
                ->cascadeOnDelete();
             $table->string('arms_category');
            $table->string('arms_number')->unique();

            $table->date('validity_date')->nullable();

            $table->string('ammunition_details')->nullable();

            $table->string('issued_from')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_arms');
    }
};
