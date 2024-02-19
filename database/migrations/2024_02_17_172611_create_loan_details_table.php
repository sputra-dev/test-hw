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
        Schema::create('loan_details', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->char('loan_id', 15);
            $table->unsignedInteger('book_id');
            $table->enum('status', ['loan_period', 'complete']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('loan_id')->references('loan_id')->on('loans')
                ->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_details');
    }
};
