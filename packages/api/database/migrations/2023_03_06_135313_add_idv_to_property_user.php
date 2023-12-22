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
            $table->string('idv_session_id')->nullable();
            $table->string('idv_client_token')->nullable();
            $table->timestamp('idv_mobile_connected_at')->nullable();
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
            $table->dropColumn('idv_session_id');
            $table->dropColumn('idv_client_token');
            $table->dropColumn('idv_mobile_connected_at');
        });
    }
};
