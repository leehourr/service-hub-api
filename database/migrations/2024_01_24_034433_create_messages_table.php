<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message_text');
            $table->timestamps();

            $table->integer('chat_id')->unsigned()->nullable();
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->integer('sender_id')->unsigned()->nullable();
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
