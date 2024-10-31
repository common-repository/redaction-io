<?php

namespace Redaction_IO\Helpers;

use Redaction_IO\Helpers;

/**
 * Class WordPress_CustomFunctions
 *
 */
class WordPress_CustomFunctions
{

    private Redaction_IO_API $Redaction_IO_API;

    function __construct() {
        $this->Redaction_IO_API = new Redaction_IO_API();
    }

    /**
     * Get post types from WordPress
     *
     * @param $return_all
     * @param $args
     * @return mixed|void
     */
    public function get_post_types( $return_all = false, $args = array() ) {
        global $wp_post_types;

        $default_args = [
            'show_ui' => true,
            'public'  => true,
        ];

        $args = wp_parse_args( $args, $default_args );

        if ( '' === $args['public'] ) {
            unset( $args['public'] );
        }

        $post_types = get_post_types($args, 'objects', 'and');

        if ( ! $return_all ) {
            unset(
                $post_types['attachment'],
                $post_types['elementor_library'],
            );
        }

        $post_types = apply_filters( 'redaction_io_post_types', $post_types, $return_all, $args );

        return $post_types;
    }

    /**
     * Generate dropdown for taxonomies by taxonomies
     *
     * @param $taxonomies
     * @return string
     */
    public function get_html_dropdown_by_taxonomies($taxonomies) {
        $html = '';

        foreach ($taxonomies as $taxonomy) {
            $taxonomy_data = get_taxonomy($taxonomy);
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ));

            if (empty($terms)) {
                continue;
            }

            // Get label for taxonomy
            if (isset($taxonomy_data->labels->search_items)) {
                $label = $taxonomy_data->labels->search_items;
            } else {
                $label = $taxonomy_data['name'];
            }

            $html .= '<div class="redaction-io-box_field">';
                $html .= '<label>' . $label . '</label>';
                $html .= '<div class="redaction-io-multi-select" data-no-select-text="' . $label . '">';
                    $html .= '<select name="redaction_io_bulk_create_taxonomies[' . $taxonomy . '][]" multiple="multiple" class="redaction-io-multi-select-default isHidden" data-items-selected="0">';
                        foreach ($terms as $term) {
                            $value = $term->term_id;
                            if ($taxonomy === 'post_tag') {
                                $value = $term->name;
                            }
                            $html .= '<option value="' . $value . '">' . $term->name . '</option>';
                        }
                    $html .= '</select>';
                    $html .= '<button class="redaction-io-multi-select_button" aria-expanded="false" type="button">';
                            $html .= '<span aria-hidden="true" class="redaction-io-multi-select_label">';
                                $html .= '<span class="redaction-io-multi-select_label_term">' . $label . '</span>';
                            $html .= '</span>';
                        $html .= '<svg class="icon icon--xxs margin-left-xxs" aria-hidden="true" viewBox="0 0 12 12"><path d="M10.947,3.276A.5.5,0,0,0,10.5,3h-9a.5.5,0,0,0-.4.8l4.5,6a.5.5,0,0,0,.8,0l4.5-6A.5.5,0,0,0,10.947,3.276Z"></path></svg>';
                    $html .= '</button>';
                    $html .= '<div class="redaction-io-multi-select_dropdown" aria-describedby="redaction-io-multi-select-id-description" id="redaction-io-multi-select-id-dropdown">';
                        $html .= '<ul class="redaction-io-multi-select_dropdown_list" role="listbox" aria-multiselectable="true">';
                            foreach ($terms as $term) {
                                $value = $term->term_id;
                                if ($taxonomy === 'post_tag') {
                                    $value = $term->name;
                                }

                                $html .= '<li class="redaction-io-multi-select_dropdown_list_option">';
                                    $html .= '<input type="checkbox" data-term-id="' . $value . '" id="redaction-io-multi-select-id-' . $term->term_id . '">';
                                    $html .= '<label class="redaction-io-multi-select__item redaction-io-multi-select__item--option" aria-hidden="true" for="redaction-io-multi-select-id-' . $term->term_id . '">';
                                        $html .= '<span>' . $term->name . '</span>';
                                    $html .= '</label>';
                                $html .= '</li>';
                            }
                        $html .= '</ul>';
                    $html .= '</div>';
                $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Get post by task ID
     *
     * @param $task_id
     * @return int[]|\WP_Post[]
     */
    public function get_posts_by_task_ID($task_id)
    {
        $posts_types = $this->get_post_types();
        $list_posts_types = [];

        foreach ($posts_types as $post_type) {
            $list_posts_types[] = $post_type->name;
        }

        $args = array(
            'post_type'     => $list_posts_types,
            'post_status'   => array('publish', 'draft'),
            'meta_query'    => array(
                array(
                    'key' => '_redaction_io_task_id',
                    'value' => $task_id,
                    'compare' => '=',
                )
            )
        );
        $posts = get_posts($args);

        return $posts;
    }

    /**
     * Format text by editor
     *
     * @param $content
     * @param $returnArray
     * @return array|mixed|string|string[]
     */
    public function format_text_by_editor($content, $returnArray = false)
    {
//        if ($this->is_gutenberg_active()) {
//            if ($returnArray) {
//                $regexTitleParagraph = '/<(p|h[1-6])[^>]*>(.*?)<\/(p|h[1-6])>/';
//                preg_match_all($regexTitleParagraph, $content, $matchesTitlesParagraphs);
//                $contentLines = [];
//
//                if (isset($matchesTitlesParagraphs[0])) {
//                    foreach ($matchesTitlesParagraphs[0] as $line) {
//                        switch ($line) {
//                            case strrpos($line, "<p>") !== false :
//                                $contentLines[] = [
//                                    'type'      => 'paragraph',
//                                    'content'   => $line
//                                ];
//                                break;
//                            case strrpos($line, "<h1>") !== false :
//                                $contentLines[] = [
//                                    'type'      => 'title',
//                                    'level'     => 1,
//                                    'content'   => $line
//                                ];
//                                break;
//                            case strrpos($line, "<h2>") !== false :
//                                $contentLines[] = [
//                                    'type'      => 'title',
//                                    'level'     => 2,
//                                    'content'   => $line
//                                ];
//                                break;
//                            case strrpos($line, "<h3>") !== false :
//                                $contentLines[] = [
//                                    'type'      => 'title',
//                                    'level'     => 3,
//                                    'content'   => $line
//                                ];
//                                break;
//                            case strrpos($line, "<h4>") !== false :
//                                $contentLines[] = [
//                                    'type'      => 'title',
//                                    'level'     => 4,
//                                    'content'   => $line
//                                ];
//                                break;
//                            case strrpos($line, "<h5>") !== false :
//                                $contentLines[] = [
//                                    'type'      => 'title',
//                                    'level'     => 5,
//                                    'content'   => $line
//                                ];
//                                break;
//                            case strrpos($line, "<h6>") !== false :
//                                $contentLines[] = [
//                                    'type'      => 'title',
//                                    'level'     => 6,
//                                    'content'   => $line
//                                ];
//                                break;
//                        }
//                    }
//                }
//
//                return $contentLines;
//            } else {
//                $regexTitle = '/<h[1-6][^>]*>(.*?)<\/h[1-6]>/';
//                $regexParagraph = '/<p*>(.*?)<\/p>/';
//                preg_match_all($regexTitle, $content, $matchesTitles);
//                preg_match_all($regexParagraph, $content, $matchesParagraphs);
//
//                // Transform titles
//                if (isset($matchesTitles[0])) {
//                    foreach ($matchesTitles[0] as $title) {
//                        switch ($title) {
//                            case strrpos($title, "<h1>") !== false :
//                                $newHTMLTitle = '<!-- wp:heading {"level":1} -->' . $title . '<!-- /wp:heading -->';
//                                $content = str_replace($title, $newHTMLTitle, $content);
//                                break;
//                            case strrpos($title, "<h2>") !== false :
//                                $newHTMLTitle = '<!-- wp:heading -->' . $title . '<!-- /wp:heading -->';
//                                $content = str_replace($title, $newHTMLTitle, $content);
//                                break;
//                            case strrpos($title, "<h3>") !== false :
//                                $newHTMLTitle = '<!-- wp:heading {"level":3} -->' . $title . '<!-- /wp:heading -->';
//                                $content = str_replace($title, $newHTMLTitle, $content);
//                                break;
//                            case strrpos($title, "<h4>") !== false :
//                                $newHTMLTitle = '<!-- wp:heading {"level":4} -->' . $title . '<!-- /wp:heading -->';
//                                $content = str_replace($title, $newHTMLTitle, $content);
//                                break;
//                            case strrpos($title, "<h5>") !== false :
//                                $newHTMLTitle = '<!-- wp:heading {"level":5} -->' . $title . '<!-- /wp:heading -->';
//                                $content = str_replace($title, $newHTMLTitle, $content);
//                                break;
//                            case strrpos($title, "<h6>") !== false :
//                                $newHTMLTitle = '<!-- wp:heading {"level":6} -->' . $title . '<!-- /wp:heading -->';
//                                $content = str_replace($title, $newHTMLTitle, $content);
//                                break;
//                        }
//                    }
//                }
//
//                // Transform paragraphs
//                if (isset($matchesParagraphs[0])) {
//                    foreach ($matchesTitles[0] as $paragraph) {
//                        $newHTMLParagraph = '<!-- wp:paragraph -->' . $paragraph . '<!-- /wp:paragraph -->';
//                        $content = str_replace($paragraph, $newHTMLParagraph, $content);
//                    }
//                }
//            }
//        }
        return $content;
    }

    /**
     * Check if Block Editor is active.
     * Must only be used after plugins_loaded action is fired.
     *
     * @return bool
     */
    public function is_gutenberg_active() {
        // Gutenberg plugin is installed and activated.
        $gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );

        // Block editor since 5.0.
        $block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );

        if ( ! $gutenberg && ! $block_editor ) {
            return false;
        }

        if ( $this->is_classic_editor_plugin_active() ) {
            $editor_option       = get_option( 'classic-editor-replace' );
            $block_editor_active = array( 'no-replace', 'block' );

            return in_array( $editor_option, $block_editor_active, true );
        }

        return true;
    }

    /**
     * Check if Classic Editor plugin is active.
     *
     * @return bool
     */
    public function is_classic_editor_plugin_active() {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
            return true;
        }

        return false;
    }

    /**
     * Update post with redaction io data
     *
     * @param $post_ID
     * @param $title
     * @param $content
     * @param $seo
     * @param $slug
     * @return int|\WP_Error
     */
    public function update_post_redaction_io($post_ID, $title, $content, $seo, $slug)
    {
        $args = [
            'ID'            => $post_ID,
            'post_title'    => $title,
            'post_content'  => $content,
            'meta_input'    => []
        ];

        if (! empty($slug)) {
            $args['post_name'] = $slug;
        }

        if (isset($seo['title']['name'])) {
            $args['meta_input'][$seo['title']['name']] = $seo['title']['value'];
        }

        if (isset($seo['description']['name'])) {
            $args['meta_input'][$seo['description']['name']] = $seo['description']['value'];
        }

        $args = apply_filters('redaction_io_update_post_webhook', $args);
        return wp_update_post($args);
    }

    /**
     * Get fields name for SEO extensions
     *
     * @param $title
     * @param $description
     * @return mixed|void
     */
    public function get_fields_name_seo_plugins($title, $description)
    {
        $seo = [
            'title' => [
                'name'              => '',
                'value'             => $title
            ],
            'description' => [
                'name'              => '',
                'value'             => $description
            ]
        ];

        if (is_plugin_active('wp-seopress/seopress.php')) {
            $seo['title']['name'] = '_seopress_titles_title';
            $seo['description']['name'] = '_seopress_titles_desc';
        } elseif (is_plugin_active('wordpress-seo/wp-seo.php')) {
            $seo['title']['name'] = '_yoast_wpseo_title';
            $seo['title']['value'] = $seo['title']['value'] . ' %%page%% %%sep%% %%sitename%%';
            $seo['description']['name'] = '_yoast_wpseo_metadesc';
        } elseif (is_plugin_active('seo-by-rank-math/rank-math.php')) {
            $seo['title']['name'] = 'rank_math_title';
            $seo['title']['value'] = $seo['title']['value'] . ' - %title% %sep% %sitename%';
            $seo['description']['name'] = 'rank_math_description';
        } elseif (is_plugin_active('autodescription/autodescription.php')) {
            $seo['title']['name'] = '_genesis_title';
            $seo['description']['name'] = '_genesis_description';
        }

        $seo = apply_filters('redaction_io_fields_name_seo_plugins', $seo, $title, $description);
        return $seo;
    }

    /**
     * Generate content for post
     *
     * @return false
     */
    public function xhr_generate_content_wp($keyword, $lang, $post_ID)
    {
        if (! empty($keyword) && ! empty($lang) && ! empty($post_ID)) {
            $response = $this->Redaction_IO_API->set_new_redaction_by_keyword($keyword, $lang);

            if ($response->status_http == 200) {
                if ($response->current_stage == null) {
                    $response->current_stage = 0;
                }

                update_post_meta($post_ID, '_redaction_io_keyword', $keyword);
                update_post_meta($post_ID, '_redaction_io_lang', $lang);
                update_post_meta($post_ID, '_redaction_io_task_id', $response->task_id);
                update_post_meta($post_ID, '_redaction_io_task_status', $response->status);
                update_post_meta($post_ID, '_redaction_io_task_current_stage', $response->current_stage);
            }

            return $response;
        }

        return false;
    }

    /**
     * Sanitize Multidimensional array
     *
     * @param $array
     * @return mixed
     */
    public function sanitize_array_redaction_io( &$array ) {
        foreach ($array as &$value) {
            if (!is_array($value)) {
                $value = sanitize_text_field($value);
            } else {
                $this->sanitize_array_redaction_io($value);
            }
        }

        return $array;
    }
}
