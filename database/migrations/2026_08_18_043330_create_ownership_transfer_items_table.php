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
        Schema::create('ownership_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ownership_transfer_id')
                ->constrained('ownership_transfers')
                ->cascadeOnDelete();

            // PlotOwner অথবা FlatOwner
            $table->morphs('owner');

            $table->enum('direction', [
                'from',
                'to',
            ]);

            $table->decimal(
                'ownership_percent',
                5,
                2
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ownership_transfer_items');
    }
};
