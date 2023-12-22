<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provided_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained('answers');
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('value')->nullable();
            $table->timestamp('provided_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provided_answers');
    }
};
