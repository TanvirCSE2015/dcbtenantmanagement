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
        Schema::create('driver_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_agreement_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('staff_id')
                    ->constrained('staffs')
                    ->cascadeOnDelete();

                $table->foreignId('vechicle_id')
                    ->constrained('vechicles')
                    ->cascadeOnDelete();

                $table->date('start_date');

                $table->date('end_date')
                    ->nullable();

                $table->boolean('is_active')
                    ->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_assignments');
    }
};
