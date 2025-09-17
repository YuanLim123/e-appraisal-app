<?php

use App\Enums\AppraisalRecordStatus;
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
        Schema::create('appraisal_records', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('purpose')->nullable();
            $table->string('grade')->nullable();
            $table->text('grade_description')->nullable();
            $table->float('total')->nullable();
            $table->string('position_period')->nullable();
            $table->string('status')->default(AppraisalRecordStatus::CREATED);
            $table->unsignedInteger('current_step')->nullable();
            $table->json('answer')->nullable();
            $table->json('feedback')->nullable();
            $table->date('review_from')->nullable();
            $table->date('review_to')->nullable();
            $table->foreignId('role_id')->nullable()->constrained();
            $table->foreignId('position_id')->nullable()->constrained();
            $table->foreignId('appraiser_id')->references('id')->on('users');
            $table->foreignId('appraisee_id')->references('id')->on('users');
            $table->foreignId('current_approver_id')->nullable()->references('id')->on('users');
            $table->foreignId('season_id')->nullable()->constrained();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisal_records');
    }
};
