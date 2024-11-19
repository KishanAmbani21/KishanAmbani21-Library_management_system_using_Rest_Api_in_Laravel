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
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->date('borrow_date');
            $table->date('due_date');
            $table->string('due_date_text')->nullable();
            $table->boolean('returned')->default(false);
            $table->date('return_date')->nullable()->after('due_date');
            $table->decimal('total_penalty')->nullable();
            $table->date('penalty_paid')->nullable()->after('returned');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("UPDATE borrows SET due_date_text = to_char(due_date, 'YYYY-MM-DD')");

        DB::statement('CREATE INDEX borrows_due_date_fulltext_idx ON borrows USING GIN(to_tsvector(\'english\', due_date_text))');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS borrows_due_date_fulltext_idx');
        Schema::dropIfExists('borrows');
    }
};
