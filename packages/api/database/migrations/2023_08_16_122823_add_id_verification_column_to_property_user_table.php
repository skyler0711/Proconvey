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
            $table->boolean('id_check_required')->default(false)->after('is_primary_user');
            $table->foreignId('id_verification_id')->nullable()->after('user_id')->constrained('id_verifications')->nullOnDelete();
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
            $table->dropColumn('id_check_required');
            $table->dropForeign('id_verification_id');
        });
    }
};
