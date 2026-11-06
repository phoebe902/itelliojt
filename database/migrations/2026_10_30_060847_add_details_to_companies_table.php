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
    Schema::table('companies', function (Blueprint $table) {
        $table->string('industry')->nullable()->index()->after('name');
        $table->unsignedSmallInteger('capacity')->default(0)->after('industry');   // total intern slots
        $table->decimal('rating', 2, 1)->nullable()->after('capacity');
        $table->string('supervisor_name')->nullable()->after('rating');            // primary supervisor
        $table->boolean('is_active')->default(true)->after('supervisor_name');
    });
}

public function down(): void
{
    Schema::table('companies', function (Blueprint $table) {
        $table->dropColumn(['industry', 'capacity', 'rating', 'supervisor_name', 'is_active']);
    });
}
};
