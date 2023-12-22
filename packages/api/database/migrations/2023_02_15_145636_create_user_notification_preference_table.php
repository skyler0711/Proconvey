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
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->boolean('getting_started_forms_completed')->default(true);
            $table->boolean('onboarding_completed')->default(true);
            $table->boolean('client_new_document_uploads')->default(true);
            $table->timestamps();
        });

        DB::update('INSERT INTO user_notification_preferences (user_id, created_at, updated_at) SELECT id, NOW(), NOW() FROM users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notification_preferences');
    }
};
