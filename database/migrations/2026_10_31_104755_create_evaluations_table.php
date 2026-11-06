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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('term')->default('term')->index();
            $table->string('evaluator_name')->nullable();
            $table->string('status')->default('pending')->index();
            $table->decimal('score', 2,1)->nullable();
            $table->date('due_on')->nullable();
            $table->date('submitted_on')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'term']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
