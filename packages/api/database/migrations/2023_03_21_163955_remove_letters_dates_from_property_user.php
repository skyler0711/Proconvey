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
        Schema::table('property_user', function (Blueprint $table) {
            $table->dropColumn('client_care_letter_completed_at');
            $table->dropColumn('terms_and_conditions_completed_at');
            $table->dropColumn('id_checks_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_user', function (Blueprint $table) {
            $table->timestamp('client_care_letter_completed_at')->nullable()->after('role');
            $table->timestamp('terms_and_conditions_completed_at')->nullable()->after('client_care_letter_completed_at');
            $table->timestamp('id_checks_completed_at')->nullable()->after('terms_and_conditions_completed_at');
        });
    }
};
