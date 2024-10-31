<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

use Redaction_IO\Helpers\WordPress_CustomFunctions;
use Redaction_IO\Helpers\Redaction_IO_API;

class redaction_io_metaboxes
{
    private WordPress_CustomFunctions $WordPress_CustomFunctions;
    private Redaction_IO_API $Redaction_IO_API;

    public function __construct()
    {
        $this->WordPress_CustomFunctions = new WordPress_CustomFunctions();
        $this->Redaction_IO_API = new Redaction_IO_API();
        $redaction_io_apikey = get_option('redaction_io_apikey');

        if (! empty ($redaction_io_apikey)) {
            add_action('add_meta_boxes', [$this, 'init_metabox']);
        }
    }

    /**
     * Init metabox
     *
     * @return void
     */
    public function init_metabox()
    {
        $redaction_io_get_post_types = $this->WordPress_CustomFunctions->get_post_types();
        $redaction_io_get_post_types = apply_filters('redaction_io_metaboxe_seo', $redaction_io_get_post_types);

        if (! empty($redaction_io_get_post_types)) {
            foreach ($redaction_io_get_post_types as $key => $value) {
                add_meta_box('redaction-io_cpt', __('Redaction.io', 'redaction-io'), [$this, 'redaction_io_cpt'], $key, 'normal', 'default');
            }
        }
    }

    public function redaction_io_cpt($hook)
    {
        $post_ID = get_the_ID();
        $_redaction_io_keyword = get_post_meta($post_ID, '_redaction_io_keyword', true);
        $_redaction_io_lang = get_post_meta($post_ID, '_redaction_io_lang', true);

        if (isset($_GET['keyword_redaction'])) {
            $_redaction_io_keyword = sanitize_text_field($_GET['keyword_redaction']);
        }
        if (isset($_GET['keyword_lang'])) {
            $_redaction_io_lang = sanitize_text_field($_GET['keyword_lang']);
        }

        $languages = $this->Redaction_IO_API->get_languages();

        require_once dirname(__FILE__) . '/metaboxes/admin-metaboxes-keyword.php';
    }
}

if (is_admin()) {
    new redaction_io_metaboxes();
}
