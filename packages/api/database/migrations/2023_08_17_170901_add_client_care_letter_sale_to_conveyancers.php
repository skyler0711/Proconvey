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
            $table->longText('client_care_letter_sale')->nullable()->after('client_care_letter');
            $table->timestamp('client_care_letter_sale_completed_at')->nullable()->after('client_care_letter_sale');
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
            $table->dropColumn('client_care_letter_sale');
            $table->dropColumn('client_care_letter_sale_completed_at');
        });
    }
};
