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
        Schema::table('appraisal_records', function (Blueprint $table) {
            $table->date('employee_agreed_at')->nullable();
            $table->date('supervisor_agreed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appraisal_records', function (Blueprint $table) {
            $table->dropColumn('employee_agreed_at');
            $table->dropColumn('supervisor_agreed_at');
        });
    }
};
