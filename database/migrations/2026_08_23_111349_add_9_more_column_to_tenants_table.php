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
        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('marital_status',['married','unmarried'])->after('date_of_birth')->default('married');
            $table->string('birth_place')->after('marital_status')->default('N/A');
            $table->text('permanent_current_address')->after('birth_place')->nullable();
            $table->string('religion')->after('permanent_current_address')->default('N/A');
            $table->string('education')->after('religion')->default('N/A');
            $table->string('email')->after('mobile')->nullable();
            $table->text('old_rental')->after('email')->nullable();
            $table->text('old_flat_owner')->after('old_rental')->default('N/A');
            $table->text('current_case')->after('old_flat_owner')->default('N/A');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'marital_status',
                'birth_place',
                'permanent_current_address',
                'religion',
                'education',
                'email',
                'old_rental',
                'old_flat_owner',
                'current_case',
            ]);
        });
    }
};
