<?php

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Filter the capability to allow other roles to use the plugin.
 *
 * @return string
 *
 * @param mixed $cap
 * @param mixed $context
 */
function redaction_io_capability($cap, $context = '') {
    $newcap = apply_filters('redaction_io_capability', $cap, $context);
    if ( ! current_user_can($newcap)) {
        return $cap;
    }

    return $newcap;
}

/**
 * Get path for file.
 *
 * @since 1.0.0
 *
 * @author Anthony Martin
 *
 * @return mixed
 *
 * @param $css
 */
function redaction_io_get_path_asset($css) {
    $map = REDACTION_IO_PLUGIN_DIR_PATH . '/build/manifest.json';
    static $hash = null;

    if ( null === $hash ) {
        $hash = file_exists( $map ) ? json_decode( file_get_contents( $map ), true ) : [];
    }

    if ( array_key_exists( $css, $hash ) ) {
        return $hash[ $css ];
    }

    return $css;
}