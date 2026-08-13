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
        Schema::create('owner_occupancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('occupancy_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('flat_owner_id')
                ->constrained('flat_owners')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_occupancies');
    }
};
