<?php

use Carbon\Carbon;
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
        $propertyUsers = DB::table('property_user')->get();

        foreach ($propertyUsers as $propertyUser) {
            $property = DB::table('properties')->where('id', $propertyUser->property_id)->first();
            $conveyancer = DB::table('conveyancers')->where('id', $property->conveyancer_id)->first();

            if (DB::table('id_verifications')
                ->where('user_id', $propertyUser->user_id)
                ->where('conveyancer_id', $conveyancer->id)
                ->exists()) {
                continue;
            }

            DB::table('id_verifications')->insert([
                'user_id' => $propertyUser->user_id,
                'conveyancer_id' => $conveyancer->id,
                'session_id' => $propertyUser->idv_session_id,
                'client_token' => $propertyUser->idv_client_token,
                'mobile_connected_at' => $propertyUser->idv_mobile_connected_at,
                'id_verification_completed_at' => $propertyUser->id_verification_completed_at,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        Schema::table('property_user', function (Blueprint $table) {
            $table->dropColumn('idv_session_id');
            $table->dropColumn('idv_client_token');
            $table->dropColumn('idv_mobile_connected_at');
            $table->dropColumn('id_verification_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
