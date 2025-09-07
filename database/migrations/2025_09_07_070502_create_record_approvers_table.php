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
        Schema::create('record_approvers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sequence');
            $table->text('comment')->nullable();
            $table->date('approved_at')->nullable();
            $table->date('rejected_at')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('appraisal_record_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_approvers');
    }
};
