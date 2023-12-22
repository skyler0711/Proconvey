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
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('sof_check_required')->after('id_check_required')->nullable();
        });

        DB::table('properties')->update([
            'sof_check_required' => false,
        ]);

        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('sof_check_required')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('sof_check_required')->nullable()->change();
        });
    }
};
