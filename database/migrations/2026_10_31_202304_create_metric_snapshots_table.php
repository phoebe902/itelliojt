<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metric_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('metric')->index();     // 'performance' or 'completion'
            $table->decimal('value', 5, 1);        // 0 - 100
            $table->date('recorded_on');
            $table->timestamps();

            $table->unique(['metric', 'recorded_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_snapshots');
    }
};