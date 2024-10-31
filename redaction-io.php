<?php
/*
Plugin Name: Redaction.io
Description: Generate Full Content SEO with AI.
Author: Redaction.io
Version: 1.0.4
Author URI: https://www.redaction.io/
License: GPLv2
Text Domain: redaction-io
Domain Path: /languages
*/

/*  Copyright 2022 - Redaction.io

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// To prevent calling the plugin directly
if ( ! function_exists('add_action')) {
    echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
    exit;
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//Define
///////////////////////////////////////////////////////////////////////////////////////////////////
define('REDACTION_IO_VERSION', '1.0.4');
define('REDACTION_IO_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('REDACTION_IO_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('REDACTION_IO_ASSETS_DIR', REDACTION_IO_PLUGIN_DIR_URL . 'assets');
define('REDACTION_IO_TEMPLATE_DIR', REDACTION_IO_PLUGIN_DIR_PATH . 'templates');

define('REDACTION_IO_DIRURL', plugin_dir_url(__FILE__));
define('REDACTION_IO_DIR_LANGUAGES', dirname(plugin_basename(__FILE__)) . '/languages/');

require_once __DIR__ . '/redaction-io-autoload.php';
require_once __DIR__ . '/redaction-io-functions.php';

///////////////////////////////////////////////////////////////////////////////////////////////////
// Translation + Init
///////////////////////////////////////////////////////////////////////////////////////////////////
function redaction_io_init($hook) {
    //i18n
    load_plugin_textdomain('redaction-io', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    if (is_admin() || is_network_admin()) {
        require_once dirname(__FILE__) . '/inc/admin/admin.php';
        require_once dirname(__FILE__) . '/inc/xhr/admin.php';
        require_once dirname(__FILE__) . '/inc/admin/metaboxes.php';
    }

    require_once dirname(__FILE__) . '/inc/rest-api/rest-api.php';
}
add_action('plugins_loaded', 'redaction_io_init', 999);
