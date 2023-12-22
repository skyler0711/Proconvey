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
        Schema::table('users', function (Blueprint $table) {
            $table->string('case_reference')->nullable();
            $table->boolean('terms_and_conditions')->default(false)->after('job_role')->nullable();
            $table->boolean('id_check')->default(false)->after('terms_and_conditions')->nullable();
            $table->boolean('payment_on_account')->default(false)->after('id_check')->nullable();
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
            $table->dropColumn('case_reference');
            $table->dropColumn('terms_and_conditions');
            $table->dropColumn('id_check');
            $table->dropColumn('payment_on_account');
        });
    }
};
