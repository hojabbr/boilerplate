<?php

namespace App\Console\Commands;

use App\Domains\Blog\Models\BlogPost;
use Illuminate\Console\Command;

class BlogSyncEmbeddingsCommand extends Command
{
    protected $signature = 'blog:sync-embeddings
                            {--chunk=50 : Number of posts per chunk}';

    protected $description = 'Backfill blog_post_chunks with embeddings for existing posts (Postgres + pgvector only).';

    public function handle(): int
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getDriverName() !== 'pgsql') {
            $this->warn('Embeddings sync is only supported on PostgreSQL with pgvector.');

            return self::FAILURE;
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('blog_post_chunks')) {
            $this->warn('Table blog_post_chunks does not exist. Run migrations on PostgreSQL first.');

            return self::FAILURE;
        }

        $chunkSize = (int) $this->option('chunk');
        $total = BlogPost::query()->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        BlogPost::query()
            ->chunkById($chunkSize, function ($posts) use ($bar): void {
                foreach ($posts as $post) {
                    $post->save();
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info("Synced embeddings for {$total} blog posts.");

        return self::SUCCESS;
    }
}
