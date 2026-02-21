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
        Schema::create('blog_post_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->text('purpose')->nullable();
            $table->text('objective')->nullable();
            $table->text('topics')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->json('days_of_week'); // 0-6
            $table->json('run_at_hours'); // 0-23, at least one required
            $table->unsignedTinyInteger('posts_per_run')->default(1);
            $table->unsignedInteger('total_posts_limit')->nullable();
            $table->string('provider');
            $table->string('length')->default('medium');
            $table->json('language_ids');
            $table->boolean('generate_image')->default(false);
            $table->boolean('generate_audio')->default(false);
            $table->boolean('publish_immediately')->default(false);
            $table->timestamp('last_run_at')->nullable();
            $table->unsignedInteger('posts_generated')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('blog_post_series_id')->nullable()->constrained('blog_post_series')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_post_series');
    }
};
