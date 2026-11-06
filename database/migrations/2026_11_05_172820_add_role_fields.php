<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student')->index();
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->index();   // active | pending | suspended
            }
            if (! Schema::hasColumn('users', 'student_number')) {
                $table->string('student_number')->nullable()->unique();
            }
            if (! Schema::hasColumn('users', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }

            $existing = array_filter(
                ['first_name', 'last_name', 'role', 'status', 'student_number', 'department'],
                fn ($column) => Schema::hasColumn('users', $column)
            );

            if ($existing) {
                $table->dropColumn($existing);
            }
        });
    }
};