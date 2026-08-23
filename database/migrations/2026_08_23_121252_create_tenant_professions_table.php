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
        Schema::create('tenant_professions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_agreement_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title_p');
            $table->string('office_name');
            $table->string('designation');
            $table->string('mobile_no');
            $table->string('office_address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_professions');
    }
};
