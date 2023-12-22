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
        Schema::table('forms', function (Blueprint $table) {
            $table->renameColumn('repeatable_step_id', 'repeatable_answer_id');
        });

        Schema::table('steps', function (Blueprint $table) {
            $table->renameColumn('repeatable_step_id', 'repeatable_answer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->renameColumn('repeatable_answer_id', 'repeatable_step_id');
        });

        Schema::table('steps', function (Blueprint $table) {
            $table->renameColumn('repeatable_answer_id', 'repeatable_step_id');
        });
    }
};
