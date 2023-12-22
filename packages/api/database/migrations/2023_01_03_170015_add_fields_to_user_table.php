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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('conveyancer_id')->after('id')->nullable()->constrained('conveyancers')->cascadeOnDelete();
            $table->string('first_name')->after('name')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('title')->after('id');
            $table->string('suffix')->after('last_name')->nullable();
            $table->string('phone')->after('email');
            $table->string('job_role')->after('phone');
            $table->text('job_bio')->after('job_role');
        });

        DB::update('update users set first_name = SUBSTRING_INDEX(SUBSTRING_INDEX(name, \' \', 1), \' \', -1), last_name = TRIM(SUBSTR(name, LOCATE(\' \', name)))');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            $table->dropColumn('conveyancer_id');
            $table->dropColumn('title');
            $table->dropColumn('suffix');
            $table->dropColumn('phone');
            $table->dropColumn('job_role');
            $table->dropColumn('job_bio');
        });

        DB::update('update users set name = CONCAT(first_name, \' \', last_name)');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->string('name')->nullable(false)->change();
        });
    }
};
