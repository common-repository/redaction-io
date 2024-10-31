<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

use Redaction_IO\Helpers\Redaction_IO_API;
use Redaction_IO\Helpers\WordPress_CustomFunctions;

class redaction_io_xhr_admin
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
        add_action('wp_ajax_redaction_io_xhr_still_pending_generation', [$this, 'xhr_still_pending_generation']);
        add_action('wp_ajax_redaction_io_xhr_generate_content', [$this, 'xhr_generate_content']);
        add_action('wp_ajax_redaction_io_xhr_verify_stage', [$this, 'xhr_verify_stage']);
        add_action('wp_ajax_redaction_io_xhr_get_dropdown_taxonomies_by_post_type', [$this, 'xhr_get_dropdown_taxonomies_by_post_type']);
    }

    /**
     * Verify pending generation
     *
     * @return void
     */
    public function xhr_still_pending_generation()
    {
        $post_ID = (! empty($_POST['post_ID']) ? intval($_POST['post_ID']) : '');
        $response = [
            'still_pending' => false,
            'step_message'  => ''
        ];

        if (! empty($post_ID)) {
            $_redaction_io_task_current_stage = get_post_meta($post_ID, '_redaction_io_task_current_stage', true);
            $_redaction_io_task_total_stage = get_post_meta($post_ID, '_redaction_io_task_total_stage', true);

            if ($_redaction_io_task_total_stage != $_redaction_io_task_current_stage) {
                $response['still_pending'] = true;
                $response['task_id'] = get_post_meta($post_ID, '_redaction_io_task_id', true);
                $response['keyword'] = get_post_meta($post_ID, '_redaction_io_keyword', true);
                $response['lang'] = get_post_meta($post_ID, '_redaction_io_lang', true);

                if ($_redaction_io_task_current_stage !== null) {
                    $response['current_stage'] = $_redaction_io_task_current_stage;
                }
                if ($_redaction_io_task_total_stage !== null) {
                    $response['total_nb_stage'] = $_redaction_io_task_total_stage;
                }

                if ($this->Redaction_IO_API->steps_text[$_redaction_io_task_current_stage]) {
                    $response['step_message'] = $this->Redaction_IO_API->steps_text[$_redaction_io_task_current_stage];
                }
            }
        }

        die(json_encode($response));
    }

    /**
     * Generate content by keyword
     *
     * @return void
     */
    public function xhr_generate_content()
    {
        $keyword = (! empty($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '');
        $lang = (! empty($_POST['lang']) ? sanitize_text_field($_POST['lang']) : '');
        $post_ID = (! empty($_POST['post_ID']) ? intval($_POST['post_ID']) : '');
        $response = false;

        $allowedAPI = $this->Redaction_IO_API->check_remaining_tokens('launch_redaction', 1);

        if (! $allowedAPI) {
            $response = [
                'error' => true,
                'message' => __('You don\'t have enough tokens', 'redaction-io')
            ];
        }

        if (empty($post_ID)) {
            $response = [
                'error' => true,
                'message' => __('Please enter a keyword', 'redaction-io')
            ];
        }

        if (! $response) {
            $response = $this->WordPress_CustomFunctions->xhr_generate_content_wp($keyword, $lang, $post_ID);
            $response->current_stage_message = $this->Redaction_IO_API->steps_text[0];
            $response->current_stage = 0;
            $response->total_nb_stage = 8;
        }

        die(json_encode($response));
    }

    /**
     * Verify status stage
     *
     * @return void
     */
    public function xhr_verify_stage()
    {
        $task_id = (! empty($_POST['task_id']) ? sanitize_text_field($_POST['task_id']) : '');
        $post_ID = (! empty($_POST['post_ID']) ? intval($_POST['post_ID']) : '');
        $response = false;

        if (empty($post_ID)) {
            $response = [
                'error' => true,
                'message' => __('Please enter a keyword', 'redaction-io')
            ];
        }

        if (empty($task_id)) {
            $response = [
                'error' => true,
                'message' => __('No task running', 'redaction-io')
            ];
        }

        if (! empty($task_id) && ! empty($post_ID)) {
            $response = $this->Redaction_IO_API->get_redaction_by_task_id($task_id);
            $_redaction_io_task_current_stage = get_post_meta($post_ID, '_redaction_io_task_current_stage', true);
            $_redaction_io_task_total_stage = get_post_meta($post_ID, '_redaction_io_task_total_stage', true);

            if ($response->status_http == 202) {
                if (empty($_redaction_io_task_total_stage)) {
                    update_post_meta($post_ID, '_redaction_io_task_total_stage', $response->total_nb_stage);
                }

                if ($_redaction_io_task_current_stage != $response->current_stage) {
                    update_post_meta($post_ID, '_redaction_io_task_current_stage', $response->current_stage);
                }
            }

            if ($response->status_http == 200) {
                update_post_meta($post_ID, '_redaction_io_task_current_stage', $_redaction_io_task_total_stage);
                update_post_meta($post_ID, '_redaction_io_task_status', 'SUCCESS');

                $response->current_stage = $_redaction_io_task_total_stage;
                $response->total_nb_stage = $_redaction_io_task_total_stage;

                $response->format_html = $this->WordPress_CustomFunctions->format_text_by_editor($response->html, true);
                $response->seo = $this->WordPress_CustomFunctions->get_fields_name_seo_plugins($response->titleseo, $response->descriptionseo);

                if (! empty($response->seo)) {
                    foreach ($response->seo as $field) {
                        update_post_meta($post_ID, $field['name'], $field['value']);
                    }
                }
            }

            $response->current_stage_message = '';
            if (isset($this->Redaction_IO_API->steps_text[$response->current_stage])) {
                $response->current_stage_message = $this->Redaction_IO_API->steps_text[$response->current_stage];
            }
        }

        die(json_encode($response));
    }

    public function xhr_get_dropdown_taxonomies_by_post_type()
    {
        $post_type = (! empty($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '');
        $response = false;

        if (empty($post_type)) {
            $response = [
                'error' => true,
                'message' => __('The post type is empty', 'redaction-io')
            ];
        }

        if (! empty($post_type)) {
            $taxonomies = get_object_taxonomies($post_type);
            $dropdowns = $this->WordPress_CustomFunctions->get_html_dropdown_by_taxonomies($taxonomies);

            $response = [
                'error'             => false,
                'html_dropdowns'    => $dropdowns
            ];
        }

        die(json_encode($response));
    }
}

if (is_admin()) {
    new redaction_io_xhr_admin();
}

