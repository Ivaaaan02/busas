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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('last_name')->default('-');
            $table->string('first_name')->default('-');
            $table->string('middle_name')->default('-');
            $table->foreignId('program_id')->constrained('programs');
            $table->string('suffix', 10)->nullable();
            $table->string('sex', 1);
            $table->string('address');
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
