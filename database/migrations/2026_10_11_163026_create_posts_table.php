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
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type')->default('artikel');
            $table->string('status')->default('draft');
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('attachments')->nullable();

            // metadata seo
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_indexable')->default(true);

            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);

            $table->index(['type', 'status', 'published_at']);

            $table->auditTrail();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
