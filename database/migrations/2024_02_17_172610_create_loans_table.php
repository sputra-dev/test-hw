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
        Schema::create('loans', function (Blueprint $table) {
            $table->char('loan_id', 15)->primary();
            $table->unsignedMediumInteger('user_id');
            $table->date('loan_date');
            $table->date('restore_date');
            $table->enum('status', ['loan_period', 'is_over', 'finished'])->default('loan_period');
            $table->mediumText('note')->nullable();
            $table->date('restore_at')->nullable();
            $table->unsignedInteger('penalty')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
