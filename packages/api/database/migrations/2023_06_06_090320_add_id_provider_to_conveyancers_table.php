<?php

use App\Enums\IDProviders;
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
            $table->string('id_provider')->after('name')->default(IDProviders::ProConvey);
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
            $table->dropColumn('id_provider');
        });
    }
};
