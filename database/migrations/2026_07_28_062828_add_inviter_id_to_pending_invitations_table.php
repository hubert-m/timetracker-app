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
        Schema::table('pending_invitations', function (Blueprint $table) {
            $table->foreignId('inviter_id')->nullable()->constrained('users')->onDelete('cascade')->after('invitable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_invitations', function (Blueprint $table) {
            //
        });
    }
};
