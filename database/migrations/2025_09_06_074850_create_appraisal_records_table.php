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
        Schema::create('appraisal_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->references('id')->on('users');
            $table->foreignId('appraisee_id')->references('id')->on('users');
            $table->foreignId('current_approver_id')->references('id')->on('users')->nullable();
            $table->foreignId('season_id')->constrained()->nullable();
            $table->string('type');
            $table->string('purpose');
            $table->string('grade');
            $table->float('total');
            $table->string('position_period')->nullable();
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('position_id');
            $table->unsignedInteger('step')->nullable();
            $table->json('department_ids');
            $table->json('comment')->nullable();
            $table->json('feedback')->nullable();
            $table->json('approver_ids');
            $table->text('description');
            $table->text('reason')->nullable();
            $table->date('review_form');
            $table->date('review_to');
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
