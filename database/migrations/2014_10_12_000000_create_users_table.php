<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name',50);
            $table->string('email',50)->unique();
            $table->string('password');
            $table->enum('roles', [1,2,3])->default(3)->comment('SUPER_ADMIN = 1', 'ADMIN = 2', 'USER = 3');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('CREATE INDEX users_fulltext_idx ON users USING GIN(to_tsvector(\'english\', name || \' \' || email || \' \' || roles))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
