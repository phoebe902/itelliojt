<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                         // BSIT
            $table->string('name');                                   // BS Information Technology
            $table->string('department')->nullable();
            $table->unsignedSmallInteger('required_hours')->default(486);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });

        Schema::dropIfExists('programs');
    }
};