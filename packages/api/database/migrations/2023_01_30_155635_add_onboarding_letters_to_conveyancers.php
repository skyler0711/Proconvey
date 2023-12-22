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
            $table->text('client_care_letter')->nullable()->after('sra_clc_number');
            $table->text('terms_and_conditions')->nullable()->after('client_care_letter');
            $table->text('letter_header')->nullable()->after('terms_and_conditions');
            $table->text('letter_footer')->nullable()->after('letter_header');
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
            $table->dropColumn('client_care_letter');
            $table->dropColumn('terms_and_conditions');
            $table->dropColumn('letter_header');
            $table->dropColumn('letter_footer');
        });
    }
};
