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
        Schema::table('provided_answers', function (Blueprint $table) {
            $table
                ->foreignId('active_form_id')
                ->after('id')
                ->nullable()
                ->constrained('active_forms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provided_answers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('active_form_id');
        });
    }
};
