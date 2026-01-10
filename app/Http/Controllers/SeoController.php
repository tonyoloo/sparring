<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fighter;

class SeoController extends Controller
{
    /**
     * Generate dynamic sitemap.xml
     */
    public function sitemap()
    {
        $fighters = Fighter::active()->take(100)->get();

        return response()->view('seo.sitemap', [
            'fighters' => $fighters
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Generate dynamic robots.txt
     */
    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n\n";
        $content .= "# Block admin areas\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /api/\n\n";
        $content .= "# Allow important directories\n";
        $content .= "Allow: /directory\n";
        $content .= "Allow: /fighters\n";
        $content .= "Allow: /professionals\n";
        $content .= "Allow: /gyms\n\n";
        $content .= "# Sitemap\n";
        $content .= "Sitemap: " . url('sitemap.xml') . "\n";

        return response($content)->header('Content-Type', 'text/plain');
    }
}
