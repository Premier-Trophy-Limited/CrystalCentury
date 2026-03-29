<?php
/**
 * Hello Elementor Child — functions.php
 */

// ── Stylesheets ──────────────────────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'hello-elementor-child',
        get_stylesheet_uri(),
        [ 'hello-elementor-style' ],
        wp_get_theme()->get( 'Version' )
    );
}, 20 );

function cc_current_lang() {
    return defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'zh-hant';
}

function cc_is_en() {
    return cc_current_lang() === 'en';
}

function cc_resolve_page_url( array $page_ids, array $path_candidates, $lang = '' ) {
    $lang = $lang ? sanitize_key( $lang ) : cc_current_lang();

    $candidate_ids = [];

    if ( isset( $page_ids[ $lang ] ) ) {
        $candidate_ids[] = (int) $page_ids[ $lang ];
    }

    if ( isset( $page_ids['default'] ) ) {
        $candidate_ids[] = (int) $page_ids['default'];
    }

    foreach ( array_unique( array_filter( $candidate_ids ) ) as $page_id ) {
        $translated_id = (int) apply_filters( 'wpml_object_id', $page_id, 'page', true, $lang );
        $page_url      = $translated_id ? get_permalink( $translated_id ) : '';

        if ( $page_url ) {
            return $page_url;
        }
    }

    foreach ( $path_candidates as $path_candidate ) {
        $path_candidate = trim( (string) $path_candidate, '/' );

        if ( ! $path_candidate ) {
            continue;
        }

        $page = get_page_by_path( $path_candidate );

        if ( $page instanceof WP_Post ) {
            return get_permalink( $page );
        }
    }

    return home_url( '/' );
}

function cc_contact_page_url( $lang = '' ) {
    return cc_resolve_page_url(
        [
            'zh-hant' => 38309,
            'en'      => 41116,
            'default' => 38309,
        ],
        [ 'contact-us', '聯絡我們' ],
        $lang
    );
}

function cc_product_category_url( $term_id, $lang = '' ) {
    $lang          = $lang ? sanitize_key( $lang ) : cc_current_lang();
    $translated_id = (int) apply_filters( 'wpml_object_id', (int) $term_id, 'product_cat', true, $lang );
    $term_link     = $translated_id ? get_term_link( $translated_id, 'product_cat' ) : '';

    if ( ! is_wp_error( $term_link ) && $term_link ) {
        return $term_link;
    }

    return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
}

function cc_brand_name() {
    return cc_is_en() ? 'Crystal Century' : '卓越獎品 Crystal Century';
}

function cc_company_name() {
    return cc_is_en() ? 'Crystal Century Ltd.' : '卓越獎品有限公司 Crystal Century Ltd.';
}

function cc_meta_copy() {
    if ( is_front_page() ) {
        return cc_is_en()
            ? [
                'title' => 'Crystal Century | Custom Trophies, Medals & Corporate Gifts in Hong Kong',
                'desc'  => 'Custom trophies, medals, crystal awards and corporate gifts for schools, institutions and events in Hong Kong.',
            ]
            : [
                'title' => '卓越獎品 | 香港精製獎盃・獎牌・企業禮品訂製',
                'desc'  => '香港本地廠商，專業訂製獎盃、獎牌、水晶獎座、銀碟及企業禮品。一站式設計及生產服務，品質保證，歡迎批量訂購。',
            ];
    }

    if ( is_shop() ) {
        return cc_is_en()
            ? [
                'title' => 'Shop Trophies, Medals & Awards | Crystal Century Hong Kong',
                'desc'  => 'Browse custom trophies, medals, crystal awards and corporate gifts for Hong Kong schools, institutions and events.',
            ]
            : [
                'title' => '產品分類 | 卓越獎品 Crystal Century',
                'desc'  => '瀏覽獎盃、獎牌、水晶獎座、木盾、銀碟及企業禮品，適合學校、機構、協會及大型活動訂製查詢。',
            ];
    }

    return null;
}

add_filter( 'option_blogname', function ( $value ) {
    return is_admin() ? $value : cc_brand_name();
} );

add_filter( 'option_blogdescription', function ( $value ) {
    if ( is_admin() ) {
        return $value;
    }

    return cc_is_en()
        ? 'Custom trophies, medals and corporate gifts in Hong Kong.'
        : '香港精製獎盃、獎牌及企業禮品訂製。';
} );

add_filter( 'wpseo_opengraph_site_name', function () {
    return cc_brand_name();
} );

add_filter( 'wpseo_schema_organization', function ( $data ) {
    if ( ! is_array( $data ) ) {
        return $data;
    }

    $data['name']                   = cc_company_name();
    $data['legalName']              = 'Crystal Century Ltd.';
    $data['alternateName']          = 'Crystal Century';
    $data['email']                  = 'info@ptrophy.com';
    $data['telephone']              = '+852 2151 3944';
    $data['description']            = cc_is_en()
        ? 'Crystal Century supplies custom trophies, medals, plaques, certificates and corporate gifts for Hong Kong schools, institutions and events.'
        : '卓越獎品為香港學校、機構及大型活動提供獎盃、獎牌、證書、水晶獎座及企業禮品訂製服務。';
    $data['sameAs']                 = array_values( array_filter( $data['sameAs'] ?? [] ) );
    if ( empty( $data['contactPoint'] ) || ! is_array( $data['contactPoint'] ) ) {
        $data['contactPoint'] = [ [] ];
    }
    $data['contactPoint'][0]['url'] = cc_contact_page_url();
    $data['contactPoint'][0]['availableLanguage'] = [ 'en', 'zh-hant' ];

    return $data;
} );

add_filter( 'wpseo_schema_website', function ( $data ) {
    if ( ! is_array( $data ) ) {
        return $data;
    }

    $data['name']          = cc_brand_name();
    $data['alternateName'] = 'Crystal Century';

    return $data;
} );

add_filter( 'wpseo_title', function ( $title ) {
    $copy = cc_meta_copy();
    return $copy ? $copy['title'] : $title;
} );

add_filter( 'wpseo_metadesc', function ( $description ) {
    $copy = cc_meta_copy();
    return $copy ? $copy['desc'] : $description;
} );

add_filter( 'wpseo_opengraph_title', function ( $title ) {
    $copy = cc_meta_copy();
    return $copy ? $copy['title'] : $title;
} );

add_filter( 'wpseo_opengraph_desc', function ( $description ) {
    $copy = cc_meta_copy();
    return $copy ? $copy['desc'] : $description;
} );

add_filter( 'wpseo_twitter_title', function ( $title ) {
    $copy = cc_meta_copy();
    return $copy ? $copy['title'] : $title;
} );

add_filter( 'wpseo_twitter_description', function ( $description ) {
    $copy = cc_meta_copy();
    return $copy ? $copy['desc'] : $description;
} );

add_filter( 'woocommerce_page_title', function ( $title ) {
    if ( ! is_shop() ) {
        return $title;
    }

    return cc_is_en() ? 'Shop' : '產品分類';
} );

add_filter( 'get_the_excerpt', function ( $excerpt, $post ) {
    if ( is_admin() || ! is_shop() || ! $post instanceof WP_Post ) {
        return $excerpt;
    }

    if ( (int) $post->ID !== (int) wc_get_page_id( 'shop' ) ) {
        return $excerpt;
    }

    $copy = cc_meta_copy();
    return $copy ? $copy['desc'] : $excerpt;
}, 10, 2 );

add_filter( 'the_title', function ( $title, $post_id ) {
    if ( is_admin() || ! $post_id || ! is_shop() ) {
        return $title;
    }

    return (int) $post_id === (int) wc_get_page_id( 'shop' )
        ? ( cc_is_en() ? 'Shop' : '產品分類' )
        : $title;
}, 10, 2 );

add_filter( 'woocommerce_get_breadcrumb', function ( $crumbs ) {
    if ( is_admin() || ! is_shop() || cc_is_en() || empty( $crumbs ) ) {
        return $crumbs;
    }

    $last = array_key_last( $crumbs );
    if ( $last !== null && isset( $crumbs[ $last ][0] ) ) {
        $crumbs[ $last ][0] = '產品分類';
    }

    return $crumbs;
}, 10 );

// ── Quote-Only Catalogue Mode ────────────────────────────────────────────────

// 1. Hide all prices site-wide
add_filter( 'woocommerce_get_price_html', '__return_empty_string' );

// 2. Remove price + add-to-cart from single product page
add_action( 'wp', function () {
    if ( ! is_product() ) return;
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
} );

// 3. Replace add-to-cart in shop/archive loop
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

// Helper: resolve contact page URL for current language
function cc_contact_url( $product_name, $product_id ) {
    $contact_url = cc_contact_page_url();

    return add_query_arg( [
        'product'    => sanitize_text_field( $product_name ),
        'product_id' => $product_id,
    ], $contact_url );
}

// 4. Add enquiry button on single product summary
add_action( 'woocommerce_single_product_summary', 'cc_single_enquiry_button', 30 );
function cc_single_enquiry_button() {
    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $is_en = cc_is_en();
    $label = $is_en ? 'Request a Quote' : '查詢報價';
    $url   = cc_contact_url( $product->get_name(), $product->get_id() );
    printf(
        '<a href="%s" class="button cc-enquiry-btn" style="background:#b8960c;color:#fff;border:none;padding:13px 32px;font-size:1rem;font-weight:600;letter-spacing:.04em;display:inline-block;margin-top:14px;border-radius:3px;text-decoration:none;">%s &rarr;</a>',
        esc_url( $url ),
        esc_html( $label )
    );
}

// 5. Add enquiry button in product loop cards
add_action( 'woocommerce_after_shop_loop_item', 'cc_loop_enquiry_button', 10 );
function cc_loop_enquiry_button() {
    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $is_en = cc_is_en();
    $label = $is_en ? 'Request a Quote' : '查詢報價';
    $url   = cc_contact_url( $product->get_name(), $product->get_id() );
    printf(
        '<a href="%s" class="button cc-enquiry-btn" style="background:#b8960c;color:#fff;border:none;font-size:.83rem;font-weight:600;display:block;text-align:center;margin:8px 12px 0;padding:9px 16px;border-radius:3px;text-decoration:none;">%s</a>',
        esc_url( $url ),
        esc_html( $label )
    );
}

// 6. Pre-fill the enquiry form product field from URL parameter (Elementor Pro)
add_filter( 'elementor_pro/forms/field_value', function ( $value, $field ) {
    if ( isset( $field['field_id'] ) && $field['field_id'] === 'product_name' ) {
        $from_url = sanitize_text_field( wp_unslash( $_GET['product'] ?? '' ) );
        if ( $from_url ) return $from_url;
    }
    return $value;
}, 10, 2 );

// 7. Disable cart / checkout for catalogue mode
add_filter( 'woocommerce_is_purchasable', '__return_false' );
add_filter( 'woocommerce_add_to_cart_validation', '__return_false' );

add_action( 'wp', function () {
    if ( is_admin() ) {
        return;
    }

    remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
    add_action( 'woocommerce_archive_description', function () {
        if ( ! is_shop() ) {
            woocommerce_product_archive_description();
            return;
        }

        $copy = cc_meta_copy();
        if ( ! $copy ) {
            return;
        }

        echo '<div class="page-description"><p>' . esc_html( $copy['desc'] ) . '</p></div>';
    }, 10 );
}, 20 );

add_filter( 'elementor/widget/render_content', function ( $content ) {
    if ( is_admin() || ! is_string( $content ) || $content === '' ) {
        return $content;
    }

    $cta_label   = cc_is_en() ? 'Contact Us' : '聯絡我們';
    $cta_url     = get_permalink( cc_is_en() ? 41116 : 38309 );

    $content = str_replace( 'Order Now!', $cta_label, $content );
    $content = preg_replace(
        '#href=(["\'])https?://www\.crystalcentury\.com/product/?\1#',
        'href=' . wp_json_encode( esc_url( $cta_url ) ),
        $content
    );

    return $content;
}, 20 );

add_filter( 'gettext', function ( $translated, $text ) {
    if ( is_admin() ) {
        return $translated;
    }

    if ( $text === 'Type to start searching...' ) {
        return cc_is_en() ? 'Search trophies, medals or gifts' : '搜尋獎盃、獎牌或禮品';
    }

    return $translated;
}, 20, 2 );

add_action( 'wp_footer', function () {
    if ( is_admin() ) {
        return;
    }

    $placeholder = cc_is_en() ? 'Search trophies, medals or gifts' : '搜尋獎盃、獎牌或禮品';
    $cta_label   = cc_is_en() ? 'Contact Us' : '聯絡我們';
    $cta_url     = cc_contact_page_url();
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('input.e-search-input').forEach(function (input) {
        if (!input.getAttribute('placeholder') || input.getAttribute('placeholder') === 'Type to start searching...') {
          input.setAttribute('placeholder', <?php echo wp_json_encode( $placeholder ); ?>);
        }
      });

      document.querySelectorAll('.e-contact-buttons__cta-button').forEach(function (link) {
        var href = link.getAttribute('href') || '';
        var text = (link.textContent || '').trim();
        if (text === 'Order Now!' || /\/product\/?$/i.test(href)) {
          link.textContent = <?php echo wp_json_encode( $cta_label ); ?>;
          link.setAttribute('href', <?php echo wp_json_encode( $cta_url ); ?>);
          link.removeAttribute('target');
          link.removeAttribute('rel');
        }
      });
    });
    </script>
    <?php
}, 99 );

// 8. Category grid shortcode — bypasses WPML's Elementor widget processing
add_shortcode( 'cc_catgrid', function( $atts ) {
    $atts = shortcode_atts( [ 'lang' => '' ], $atts );

    $lang = $atts['lang'] ? sanitize_key( $atts['lang'] ) : cc_current_lang();

    // Category slug → EN/ZH pairs: [en_slug, zh_slug, EN label, ZH label, EN term_id, ZH term_id]
    $cats = [
        [ 'trophy',              '獎盃',     'Trophy',              '獎盃',     347, 333 ],
        [ 'medal',               '獎牌',     'Medal',               '獎牌',     346, 332 ],
        [ 'crystal-trophy',      '水晶獎座', 'Crystal Trophy',      '水晶獎座', 345, 331 ],
        [ 'plaque',              '木盾',     'Plaque',              '木盾',     344, 339 ],
        [ 'commemorative-plate', '銀碟',     'Commemorative Plate', '銀碟',     350, 335 ],
        [ 'certificate',         '證書',     'Certificate',         '證書',     349, 338 ],
        [ 'flag',                '旗幟',     'Flag',                '旗幟',     343, 334 ],
        [ 'commemorative-gift',  '廣告禮品', 'Commemorative Gift',  '廣告禮品', 342, 340 ],
    ];

    $is_en   = ( $lang === 'en' );
    $html    = '';

    foreach ( $cats as $c ) {
        $label = $is_en ? $c[2] : $c[3];
        $term_id   = $is_en ? $c[4] : $c[5];
        $url       = cc_product_category_url( $term_id, $lang );
        $thumb_id  = (int) get_term_meta( $term_id, 'thumbnail_id', true );
        $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : wc_placeholder_img_src( 'medium' );

        $html .= '<div class="elementor-element elementor-widget elementor-widget-image-box cc-catcard">';
        $html .= '<div class="elementor-widget-container">';
        $html .= '<div class="elementor-image-box-wrapper">';
        $html .= '<figure class="elementor-image-box-img">';
        $html .= '<a href="' . esc_url( $url ) . '" tabindex="-1">';
        $html .= '<img src="' . esc_url( $thumb_url ) . '" alt="' . esc_attr( $label ) . '" loading="lazy" width="300" height="300">';
        $html .= '</a></figure>';
        $html .= '<div class="elementor-image-box-content">';
        $html .= '<h3 class="elementor-image-box-title"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></h3>';
        $html .= '</div></div></div></div>';
    }

    return $html;
} );
