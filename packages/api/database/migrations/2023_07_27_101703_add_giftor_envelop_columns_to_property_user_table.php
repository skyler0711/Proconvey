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
            $table->string('giftor_declaration_envelope_id')->nullable()->after('letters_envelope_token');
            $table->string('giftor_declaration_envelope_token')->nullable()->after('giftor_declaration_envelope_id');
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
            $table->dropColumn('giftor_declaration_envelope_id', 'giftor_declaration_envelope_token');
        });
    }
};
