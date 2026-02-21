<?php

namespace App\Core\Inertia;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

/**
 * Resolves Inertia page component names to file paths for testing.
 * Mirrors frontend resolution: feature pages under resources/js/features/<name>/pages/,
 * with "welcome" mapped to features/landing/pages/welcome.
 */
class TestingViewFinder
{
    /**
     * @param  array<string>  $extensions
     */
    public function __construct(
        protected Filesystem $files,
        protected array $extensions = ['tsx', 'ts', 'jsx', 'js'],
    ) {}

    /**
     * Find the full path for an Inertia page component name.
     *
     * @throws InvalidArgumentException
     */
    public function find(string $name): string
    {
        $base = resource_path('js');
        $candidates = $this->candidatePaths($name, $base);

        foreach ($candidates as $path) {
            foreach ($this->extensions as $ext) {
                $full = $path.'.'.$ext;
                if ($this->files->exists($full)) {
                    return $full;
                }
            }
        }

        throw new InvalidArgumentException("Inertia page component file [{$name}] does not exist.");
    }

    /**
     * @return list<string>
     */
    protected function candidatePaths(string $name, string $base): array
    {
        if ($name === 'welcome') {
            return [$base.'/features/landing/pages/welcome'];
        }

        $parts = explode('/', $name);
        $feature = $parts[0];
        $pageFile = count($parts) > 1 ? implode('/', array_slice($parts, 1)) : $name;
        $featurePath = $base.'/features/'.$feature.'/pages/'.$pageFile;
        $pagesPath = $base.'/pages/'.$name;

        return [$featurePath, $pagesPath];
    }
}
