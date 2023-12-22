<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->boolean('is_primary_user')->default(false)->after('role');
        });

        DB::table('property_user')
            ->whereIn('role', ['owner', 'buyer', 'remortgager'])
            ->update([
                'is_primary_user' => true,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_user', function (Blueprint $table) {
            $table->dropColumn('is_primary_user');
        });
    }
};
