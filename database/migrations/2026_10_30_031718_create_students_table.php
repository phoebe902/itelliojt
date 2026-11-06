<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('required_hours')->default(486);
            $table->unsignedSmallInteger('rendered_hours')->default(0);
            $table->string('risk_level')->default('on_track')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};