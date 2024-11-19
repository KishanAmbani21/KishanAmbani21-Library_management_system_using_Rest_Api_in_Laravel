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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title', 50);
            $table->string('author', 50);
            $table->string('isbn', 13)->unique();
            $table->enum('status', [1,2])->default(1)->comment('AVAILABLE = 1', 'NOT_AVAILABLE = 2');
            $table->date('publication_date');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement('CREATE INDEX books_fulltext_idx ON books USING GIN(to_tsvector(\'english\', title || \' \' || author || \' \' || isbn))');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
