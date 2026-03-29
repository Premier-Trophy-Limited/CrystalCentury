<?php
if (!defined('ABSPATH')) { exit; }
add_action('send_headers', function(){ if (!headers_sent()) { header_remove('X-Robots-Tag'); } }, 999);
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
add_action('wp_footer', function () { wp_deregister_script('wp-embed'); }, 1);
add_action('wp_enqueue_scripts', function () {
    if (!is_user_logged_in()) wp_deregister_style('dashicons');
    if (!is_cart() && !is_checkout() && !is_account_page()) {
        wp_dequeue_script('wc-cart-fragments');
        wp_deregister_script('wc-cart-fragments');
    }
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('global-styles');
}, 99);
add_action('wp_default_scripts', function ($scripts) {
    if (is_admin() || !isset($scripts->registered['jquery'])) return;
    $jquery = $scripts->registered['jquery'];
    if (isset($jquery->deps) && is_array($jquery->deps)) $jquery->deps = array_diff($jquery->deps, ['jquery-migrate']);
});
add_filter('loop_shop_per_page', function($cols){ return 12; }, 99);


// Design polish layer (lightweight, Elementor-compatible)
add_action('wp_head', function(){
    if (is_admin()) return;
    echo '<style id="arby-design-polish">
.elementor-widget-heading h1,.elementor-widget-heading h2,.elementor-widget-heading h3{letter-spacing:.2px}
.elementor-section{scroll-margin-top:90px}
img{image-rendering:auto}
.site-footer a,footer a{text-decoration:none}
</style>';
}, 99);
