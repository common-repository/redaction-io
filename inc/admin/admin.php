<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

use Redaction_IO\Helpers\Redaction_IO_API;
use Redaction_IO\Helpers\WordPress_CustomFunctions;

class redaction_io_options
{
    /**
     * Holds the values to be used in the fields callbacks.
     */
    private Redaction_IO_API $Redaction_IO_API;
    private WordPress_CustomFunctions $WordPress_CustomFunctions;

    /**
     * Start up.
     */
    public function __construct()
    {
        $this->Redaction_IO_API = new Redaction_IO_API();
        $this->WordPress_CustomFunctions = new WordPress_CustomFunctions();
        add_action('admin_enqueue_scripts', [$this, 'add_admin_options_scripts'], 10, 1);
        add_action('admin_menu', [$this, 'add_plugin_page'], 10);
        add_action('current_screen', [$this, 'current_screen']);
        add_action('admin_init', [$this, 'page_init']);
        add_action('admin_init', [$this, 'redaction_io_feature_save'], 30);
    }

    public function add_admin_options_scripts($hook)
    {
        global $typenow, $pagenow;

        wp_register_style('redaction-io-fonts', REDACTION_IO_DIRURL . redaction_io_get_path_asset('build/css/fonts.css'), [], REDACTION_IO_VERSION);
        wp_register_style('redaction-io-admin', REDACTION_IO_DIRURL . redaction_io_get_path_asset('build/redaction-io.css'), [], REDACTION_IO_VERSION);
        wp_enqueue_style('redaction-io-admin');
        wp_enqueue_style('redaction-io-fonts');

        $posts_types = $this->WordPress_CustomFunctions->get_post_types();

        if (array_key_exists($typenow, $posts_types)) {;
            wp_register_script('redaction-io-metaboxes', REDACTION_IO_DIRURL . redaction_io_get_path_asset('build/redaction-io-metaboxes.js'), [], REDACTION_IO_VERSION);
            wp_enqueue_script('redaction-io-metaboxes');

            $redaction_io_data = array(
                'link' => admin_url( 'admin-ajax.php' ),
                'apikey' => get_option('redaction_io_apikey')
            );
            wp_localize_script('redaction-io-metaboxes', 'redaction_io_ajax_object', $redaction_io_data);
        }

        if ($pagenow === 'admin.php' && isset($_GET['page']) && $_GET['page'] === 'redaction-io-bulk-create') {
            wp_register_script('redaction-io-option-page', REDACTION_IO_DIRURL . redaction_io_get_path_asset('build/redaction-io-option-page.js'), [], REDACTION_IO_VERSION);
            wp_enqueue_script('redaction-io-option-page');

            $redaction_io_data = array(
                'link' => admin_url( 'admin-ajax.php' )
            );
            wp_localize_script('redaction-io-option-page', 'redaction_io_ajax_object', $redaction_io_data);
        }
    }

    /**
     * Adds custom functionality to "Seoquantum" admin pages.
     *
     * @since   1.0.0
     *
     * @param   void
     * @return  void
     */
    public function current_screen( $screen )
    {
        // Determine if the current page being viewed is "ACF" related.
        if ( isset( $screen->base ) && ($screen->base === 'toplevel_page_redaction-io-option' || $screen->base === 'redaction-io_page_redaction-io-bulk-create') ) {
            add_action('in_admin_header', [$this, 'in_admin_header']);
        }
    }

    /**
     * Renders the admin navigation element.
     *
     * @date    27/3/20
     * @since   5.9.0
     *
     * @param   void
     * @return  void
     */
    public function in_admin_header() {
         require_once dirname(__FILE__) . '/admin-pages/partials/html-top.php';
    }

    public function redaction_io_feature_save()
    {
        $html = '';
        if (isset($_GET['settings-updated']) && 'true' === $_GET['settings-updated']) {
            $html .= '<div class="redaction-io-notice-save">';
        } else {
            $html .= '<div class="redaction-io-notice-save" style="display: none">';
        }
        $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true"><path d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z"></path></svg>
                <p>' . __('Your API key has been registered.', 'redaction-io') . '</p>
        </div>';

        return $html;
    }

    /**
     * Add options page.
     */
    public function add_plugin_page()
    {
        $redaction_io_apikey = get_option('redaction_io_apikey');
        $sq_admin_menu['icon'] = 'dashicons-admin-redaction-io';
        $sq_admin_menu['title'] = __('Redaction.io', 'redaction-io');

        add_menu_page(__('Redaction.io Option Page', 'redaction-io'), $sq_admin_menu['title'], redaction_io_capability('manage_options', 'menu'), 'redaction-io-option', [$this, 'create_admin_page'], $sq_admin_menu['icon'], 90);
        add_submenu_page('redaction-io-option', __('API', 'redaction-io'), __('API', 'redaction-io'), redaction_io_capability('manage_options', 'menu'), 'redaction-io-option', [$this, 'create_admin_page']);
        if (! empty($redaction_io_apikey)) {
            add_submenu_page('redaction-io-option', __('Bulk create', 'redaction-io'), __('Bulk create', 'redaction-io'), redaction_io_capability('manage_options', 'menu'), 'redaction-io-bulk-create', [$this, 'html_bulk_create_page']);
        }
    }

    /**
     * Generate HTML for API page
     *
     * @return void
     */
    public function create_admin_page()
    {
        require_once dirname(__FILE__) . '/admin-pages/main.php';
    }

    /**
     * Generate HTML for bulk create page
     *
     * @return void
     */
    public function html_bulk_create_page()
    {
        $redaction_io_bulk_create_post_type = (isset($_POST['redaction_io_bulk_create_post_type']) ? sanitize_text_field($_POST['redaction_io_bulk_create_post_type']) : '');
        $redaction_io_bulk_create_keywords = (isset($_POST['redaction_io_bulk_create_keywords']) ? sanitize_textarea_field($_POST['redaction_io_bulk_create_keywords']) : '');
        $redaction_io_bulk_create_lang = (isset($_POST['redaction_io_bulk_create_lang']) ? sanitize_text_field($_POST['redaction_io_bulk_create_lang']) : '');
        $redaction_io_bulk_create_taxonomies = (isset($_POST['redaction_io_bulk_create_taxonomies']) ? $this->WordPress_CustomFunctions->sanitize_array_redaction_io($_POST['redaction_io_bulk_create_taxonomies']) : array());
        if (! empty($redaction_io_bulk_create_post_type) && ! empty($redaction_io_bulk_create_keywords) && ! empty($redaction_io_bulk_create_lang)) {
            $response = $this->generate_keyword_as_post($redaction_io_bulk_create_post_type, $redaction_io_bulk_create_keywords, $redaction_io_bulk_create_lang, $redaction_io_bulk_create_taxonomies);
        }

        $languages = $this->Redaction_IO_API->get_languages();
        $posts_types = $this->WordPress_CustomFunctions->get_post_types();

        if (is_array($posts_types)) {
            $first_post_type = array_key_first($posts_types);
            if (!empty($first_post_type)) {
                $taxonomies = get_object_taxonomies($first_post_type);
                $dropdown = $this->WordPress_CustomFunctions->get_html_dropdown_by_taxonomies($taxonomies);
            }
        }

        require_once dirname(__FILE__) . '/admin-pages/bulk-create/bulk-create.php';
    }

    public function page_init()
    {
        register_setting(
            'redaction_io_option_group',
            'redaction_io_apikey',
            'sanitize_text_field'
        );
    }

    /**
     * Generate post by keyword
     *
     * @param $post_type
     * @param $keywords
     * @param $lang
     * @param array $taxonomies
     * @return false|array
     */
    public function generate_keyword_as_post($post_type, $keywords, $lang, array $taxonomies = [])
    {
        if (empty($post_type) || empty($keywords) || empty($lang)) {
            return false;
        }

        $response = [
            'error' => false,
            'message' => __('Publications are being generated', 'redaction-io'),
            'redirectLink' => admin_url('edit.php?post_type=' . $post_type)
        ];
        $keywords_list = preg_split("/[\s]*[,]|[;]|[\r\n][\s]*/", $keywords);
        $allowedAPI = $this->Redaction_IO_API->check_remaining_tokens('launch_redaction', count($keywords_list));

        if (! $allowedAPI) {
            $response = [
                'error' => true,
                'message' => __('You don\'t have enough tokens', 'redaction-io'),
            ];
        } else {
            foreach ($keywords_list as $keyword) {

                // Pass if empty
                if (empty($keyword)) {
                    continue;
                }

                // Get current user ID
                $user_ID = get_current_user_id();

                // Create post object
                $args = array(
                    'post_title'    => __('[Redaction.io] Currently being created for the keyword: ', 'redaction-io') . $keyword,
                    'post_status'   => 'draft',
                    'post_content'  => '',
                    'post_type'     => $post_type,
                    'post_author'   => ($user_ID ?? 1),
                );

                // Insert the post into the database
                $post_ID = wp_insert_post($args);

                // Set taxonomies
                if (! empty($taxonomies)) {
                    foreach ($taxonomies as $taxonomy => $terms) {
                        if ($taxonomy === 'post_tag') {
                            wp_set_post_tags($post_ID, $terms);
                        } else {
                            wp_set_post_terms($post_ID, $terms, $taxonomy);
                        }
                    }
                }

                // Postmeta
                update_post_meta($post_ID, '_redaction_io_regenerate_slug', true);

                // Call API
                $responseAPI = $this->WordPress_CustomFunctions->xhr_generate_content_wp($keyword, $lang, $post_ID);

                if ($responseAPI === false) {
                    $response['error'] = true;
                    $response['message'] = __('An error occurred while generating content', 'redaction-io');
                }
            }
        }

        return $response;
    }
}

if (is_admin()) {
    $my_settings_page = new redaction_io_options();
}
