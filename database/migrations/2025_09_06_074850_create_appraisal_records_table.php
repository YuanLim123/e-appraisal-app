<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\AppraisalRecordStatus;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appraisal_records', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('purpose');
            $table->string('grade');
            $table->float('total');
            $table->string('position_period')->nullable();
            $table->enum('status', AppraisalRecordStatus::cases())->default(AppraisalRecordStatus::CREATED);
            $table->unsignedInteger('current_step')->nullable();
            $table->json('answer')->nullable();
            $table->json('feedback')->nullable();
            $table->text('description');
            $table->date('review_form');
            $table->date('review_to');
            $table->foreignId('role_id')->constrained();
            $table->foreignId('position_id')->constrained();
            $table->foreignId('appraiser_id')->references('id')->on('users');
            $table->foreignId('appraisee_id')->references('id')->on('users');
            $table->foreignId('current_approver_id')->references('id')->on('users')->nullable();
            $table->foreignId('season_id')->constrained()->nullable();
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
