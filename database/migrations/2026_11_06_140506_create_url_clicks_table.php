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
        Schema::create('url_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('short_url_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->string('referer', 2048)->nullable();
            $table->timestamp('clicked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_clicks');
    }
};
