<?php

namespace App\Domains\Sitemap\Http\Controllers;

use App\Core\Services\SitemapGenerator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController
{
    /**
     * Generate and return the sitemap XML.
     */
    public function __invoke(SitemapGenerator $generator): Response
    {
        $xml = Cache::rememberForever(SitemapGenerator::CACHE_KEY, function () use ($generator): string {
            $entries = $generator->generate();

            $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

            foreach ($entries as $entry) {
                $xml .= "    <url>\n";
                $xml .= '        <loc>'.htmlspecialchars($entry['loc'], ENT_XML1, 'UTF-8')."</loc>\n";

                if ($entry['lastmod'] !== null) {
                    $xml .= '        <lastmod>'.htmlspecialchars($entry['lastmod'], ENT_XML1, 'UTF-8')."</lastmod>\n";
                }

                $xml .= '        <changefreq>'.htmlspecialchars($entry['changefreq'], ENT_XML1, 'UTF-8')."</changefreq>\n";
                $xml .= '        <priority>'.number_format($entry['priority'], 1)."</priority>\n";
                $xml .= "    </url>\n";
            }

            $xml .= '</urlset>';

            return $xml;
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
            'ETag' => '"'.md5($xml).'"',
        ]);
    }
}
