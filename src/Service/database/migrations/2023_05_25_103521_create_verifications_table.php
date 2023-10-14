<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('verifications', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->ipAddress('ip');
            $table->string('verify_value')->comment('значение адреса на который идет запрос: номер телефона, email адрес и тд');
            $table->string('channel')->comment('канал по которому происходит верификация: телефон, почта и т.д.');
            $table->string('code')->comment('секертный код');
            $table->integer('attempts')->nullable()->comment('номер попытки');
            $table->string('status');
            $table->timestamp('expires_at');
            $table->timestamp('timeout')->nullable();

            $table->timestamps();

            $table->index('ip');
            $table->index('verify_value');
            $table->index('code');

            /** составной индекс из-за частой юзабильности этих полей в работе модуля */
            $table->index(['ip', 'verify_value', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('verifications');
    }
};
