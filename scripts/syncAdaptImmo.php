<?php
    if( php_sapi_name() !== 'cli' ) {
//        die("Meant to be run from command line");
    }
    if(!defined('ABSPATH')) define('ABSPATH', __DIR__."/../../../../" );
    require_once(ABSPATH.'wp-load.php');
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    require_once(__DIR__.'/../libs/syncro/import.php');
    require_once(__DIR__.'/../libs/syncro/import_pictures.php');

    // ini_set('max_execution_time', '0');
    ini_set('max_execution_time', '600');
    ini_set('memory_limit', '4096M');
    syncProperties(true);
    syncPropertiesPictures( true);