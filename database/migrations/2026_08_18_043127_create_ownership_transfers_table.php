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
        Schema::create('ownership_transfers', function (Blueprint $table) {
            $table->id();
             // Plot অথবা Flat
            $table->morphs('ownable');

            $table->enum('transfer_type', [
                'purchase',
                'inheritance',
                'gift',
                'transfer',
                'other',
            ]);

            $table->date('transfer_date');

            $table->string('document_no')
                ->nullable();

            $table->string('document_file')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ownership_transfers');
    }
};
