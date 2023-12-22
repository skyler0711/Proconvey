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
            $table->string('trading_name')->nullable()->after('sra_clc_number');
            $table->string('vat_number')->nullable()->after('trading_name');
            $table->string('website')->nullable()->after('vat_number');
            $table->string('location')->nullable()->after('website');
            $table->string('telephone_number')->nullable()->after('location');
            $table->string('email_address')->nullable()->after('telephone_number');
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
            $table->dropColumn('trading_name');
            $table->dropColumn('vat_number');
            $table->dropColumn('website');
            $table->dropColumn('location');
            $table->dropColumn('telephone_number');
            $table->dropColumn('email_address');
        });
    }
};
