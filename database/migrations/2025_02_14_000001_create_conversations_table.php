<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Optional: for group chats or custom naming
            $table->timestamps();
        });

        Schema::create('conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            // Add conversation_id to messages table
            $table->foreignId('conversation_id')->after('id')->constrained()->cascadeOnDelete();
            
            // We'll keep sender_id but remove recipient_id as it's now handled by conversation_user
            $table->dropForeign(['recipient_id']);
            $table->dropColumn('recipient_id');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
            $table->foreignId('recipient_id')->after('sender_id')->constrained('users');
        });

        Schema::dropIfExists('conversation_user');
        Schema::dropIfExists('conversations');
    }
};
