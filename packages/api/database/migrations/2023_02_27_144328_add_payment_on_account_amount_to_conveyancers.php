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
        Schema::table('conveyancers', function (Blueprint $table) {
            $table->integer('payment_on_account_amount')->unsigned()->nullable()->after('stripe_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conveyancers', function (Blueprint $table) {
            $table->dropColumn('payment_on_account_amount');
        });
    }
};
