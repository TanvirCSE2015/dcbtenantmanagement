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
        Schema::create('occupancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flat_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('occupancy_type', [
                'owner',
                'tenant',
                'vacant',
            ]);

            $table->date('start_date');

            $table->date('end_date')
                ->nullable();

            $table->boolean('is_current')
                ->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupancies');
    }
};
