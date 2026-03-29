<?php

$permalinks                 = get_option( 'woocommerce_permalinks', [] );
$permalinks['product_base'] = '/products/%product_cat%';
update_option( 'woocommerce_permalinks', $permalinks );

$titles = get_option( 'wpseo_titles', [] );

$titles['website_name']           = '卓越獎品 Crystal Century';
$titles['alternate_website_name'] = 'Crystal Century';
$titles['company_name']           = '卓越獎品有限公司 Crystal Century Ltd.';
$titles['company_alternate_name'] = 'Crystal Century';
$titles['org-description']        = '卓越獎品為香港學校、機構及大型活動提供獎盃、獎牌、證書、水晶獎座及企業禮品訂製服務。';
$titles['org-phone']              = '+852 2151 3944';
$titles['org-legal-name']         = 'Crystal Century Ltd.';
$titles['open_graph_frontpage_title'] = '%%sitename%%';

update_option( 'wpseo_titles', $titles );

wp_update_post( [
    'ID'           => 38903,
    'post_title'   => '卓越獎品 Crystal Century',
    'post_excerpt' => '卓越獎品 Crystal Century',
] );

update_post_meta( 38903, '_wp_attachment_image_alt', '卓越獎品 Crystal Century' );

flush_rewrite_rules();

echo "Live option fixes applied\n";
