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
        Schema::create('users', function (Blueprint $table) {
            // Custom primary key instead of id()
            $table->increments('userid');

            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('username', 15)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->tinyInteger('is_active')->default(1);

            $table->dateTime('web_last_login_at')->nullable();
            $table->dateTime('app_last_login_at')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 100)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedInteger('userid')->nullable()->index();
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
