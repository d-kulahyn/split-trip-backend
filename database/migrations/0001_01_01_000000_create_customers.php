<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default(null);
            $table->string('email')->unique()->nullable()->default(null);
            $table->string('password')->nullable()->default(null);
            $table->string('avatar', 255)->nullable()->default(null);
            $table->string('social_id')->nullable()->default(null);
            $table->string('social_type')->nullable()->default(null);
            $table->boolean('need_to_change_password')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->string('avatar_color')->nullable()->default(null);
            $table->string('firebase_cloud_messaging_token')->nullable()->default(null);
            $table->string('debt_reminder_period')->nullable()->default(null);
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('sessions');
    }
};
