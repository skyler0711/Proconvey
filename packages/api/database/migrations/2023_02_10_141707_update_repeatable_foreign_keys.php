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
        Schema::table('steps', function (Blueprint $table) {
            $table->dropForeign('steps_repeatable_step_id_foreign');
            $table->foreign('repeatable_answer_id')->references('id')->on('answers')->nullOnDelete();
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropForeign('forms_repeatable_step_id_foreign');
            $table->foreign('repeatable_answer_id')->references('id')->on('answers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('steps', function (Blueprint $table) {
            $table->dropForeign('steps_repeatable_answer_id_foreign');
            $table->foreign('repeatable_answer_id')->references('id')->on('steps')->nullOnDelete();
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropForeign('forms_repeatable_answer_id_foreign');
            $table->foreign('repeatable_answer_id')->references('id')->on('steps')->nullOnDelete();
        });
    }
};
