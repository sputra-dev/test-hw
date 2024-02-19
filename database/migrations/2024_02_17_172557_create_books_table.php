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
        Schema::create('books', function (Blueprint $table) {
            $table->unsignedInteger('id')->autoIncrement();
            $table->unsignedMediumInteger('category_id');
            $table->string('title', 225);
            $table->text('cover')->nullable();
            $table->string('author', 200);
            $table->string('publisher', 200);
            $table->date('publish_year');
            $table->enum('status', ['true', 'false'])->default('true');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id')->references('id')->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
