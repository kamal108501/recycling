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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('api_url')->nullable();
            $table->string('project')->nullable();
            $table->string('method', 10)->nullable();

            $table->longText('request_params')->nullable();
            $table->longText('response')->nullable();

            $table->decimal('execution_time', 10, 2)->nullable();

            $table->unsignedBigInteger('created_by')->default(0);

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
