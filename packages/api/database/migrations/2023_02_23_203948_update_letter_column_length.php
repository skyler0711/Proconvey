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
            $table->longText('client_care_letter')->nullable()->change();
            $table->longText('terms_and_conditions')->nullable()->change();
            $table->longText('letter_header')->nullable()->change();
            $table->longText('letter_footer')->nullable()->change();
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
            $table->text('client_care_letter')->nullable()->change();
            $table->text('terms_and_conditions')->nullable()->change();
            $table->text('letter_header')->nullable()->change();
            $table->text('letter_footer')->nullable()->change();
        });
    }
};
