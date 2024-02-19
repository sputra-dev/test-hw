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
        Schema::create('restores', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->char('loan_id', 15);
            $table->date('restore_date');
            $table->enum('status', ['incomplete', 'complete']);
            $table->unsignedInteger('penalty')->nullable();
            $table->mediumText('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('loan_id')->references('loan_id')->on('loans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restores');
    }
};
