<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit( 1 );
}

do_action( 'litespeed_purge_all' );
echo "LiteSpeed cache purged\n";
