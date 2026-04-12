<?php
/**
 * Plugin Name: Strip X-Robots-Tag from XML
 * Description: Removes incorrect noindex headers from Yoast sitemaps and other XML files.
 * Author: CrystalCentury Ops
 * Version: 1.0
 */

add_action('send_headers', function() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (stripos($uri, 'sitemap') !== false || substr($uri, -4) === '.xml') {
        header_remove('X-Robots-Tag');
        header('X-Robots-Tag: index, follow', true);
    }
});
