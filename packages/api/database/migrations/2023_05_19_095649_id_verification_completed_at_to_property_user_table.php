<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('property_user', function (Blueprint $table) {
            $table->timestamp('id_verification_completed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('property_user', function (Blueprint $table) {
            $table->dropColumn('id_verification_completed_at');
        });
    }
};
