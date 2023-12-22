<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->foreignId('user_id')->after('property_id')->nullable()->constrained()->cascadeOnDelete();
        });

        DB::update('update provided_answers set user_id = (select user_id from property_user where property_user.property_id = provided_answers.property_id limit 1)');

        Schema::table('provided_answers', function (Blueprint $table) {
            $table->foreignId('user_id')->after('property_id')->nullable(false)->change();
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
            $table->dropColumn('user_id');
        });
    }
};
