<?php

use App\Enums\AppraisalRecordGrade;
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
            $table->enum('grade', AppraisalRecordGrade::cases())->nullable()->change();
            $table->float('total')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->date('review_from')->nullable()->change();
            $table->date('review_to')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appraisal_records', function (Blueprint $table) {
            $table->string('grade')->change();
            $table->float('total')->change();
            $table->text('description')->change();
            $table->date('review_from')->change();
            $table->date('review_to')->change();
        });
    }
};
