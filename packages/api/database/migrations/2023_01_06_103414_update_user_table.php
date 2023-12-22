<?php

use App\Enums\UserRole;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('id');
            $table->string('title')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->text('job_role')->nullable()->change();
            $table->text('job_bio')->nullable()->change();
        });

        DB::table('users')->update([
            'role' => UserRole::Admin,
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->string('title')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->text('job_role')->nullable(false)->change();
            $table->text('job_bio')->nullable(false)->change();
        });
    }
};
