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
        Schema::create('tenant_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_agreement_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('relation');
            $table->string('nid_no')->nullable();
            $table->string('education');
            $table->string('mobile')
                ->nullable();

            $table->date('date_of_birth')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_family_members');
    }
};
