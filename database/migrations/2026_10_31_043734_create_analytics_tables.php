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
        Schema::create('forecast_snapshots', function (Blueprint $table) {
            $table->id();
            $table->decimal('success_rate', 4,1);
            $table->decimal('model_confidence', 4,1);
            $table->timestamp('calculated_at');
            $table->timestamps();
        });

        Schema::create('competencies', function(Blueprint $table){
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('benchmark');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table-> timestamps();


        });

        Schema::create('competency_student', function(Blueprint $table){
            $table-> id();
            $table->foreignId('competency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->unique(['competency_id', 'student_id']);


        });

        Schema::create('risk_signals', function(Blueprint $table){

            $table-> id();
            $table->string('label');
            $table->timestamps();

        });

        Schema::create('risk_signal_student', function(Blueprint $table){
            $table-> id();
            $table-> foreignId('risk_signal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unique(['risk_signal_id', 'student_id']);

        });
        Schema::create('interventions', function(Blueprint $table){
            $table->id();
            $table-> foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->boolean('improved')->default(false);
            $table->date('intervened_on')->index();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions');
        Schema::dropIfExists('risk_signal_student');
        Schema::dropIfExists('risk_signals');
        Schema::dropIfExists('competency_student');
        Schema::dropIfExists('competencies');
        Schema::dropIfExixsts('forecast_snapshots');
    }
};
