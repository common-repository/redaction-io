<?php

namespace Redaction_IO\RestAPI;

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

use Redaction_IO\Helpers\WordPress_CustomFunctions;
use Redaction_IO\Helpers\Redaction_IO_API;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class redaction_io_rest_api
{
    private WordPress_CustomFunctions $WordPress_CustomFunctions;
    private Redaction_IO_API $Redaction_IO_API;

    public function __construct()
    {
        $this->WordPress_CustomFunctions = new WordPress_CustomFunctions();
        $this->Redaction_IO_API = new Redaction_IO_API();

        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $version = '1';
        $namespace = 'redaction-io/v' . $version;
        $base = 'completed-task';

        register_rest_route( $namespace, '/' . $base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'update_pending_tasks'],
                'permission_callback' => [$this, 'permissions_check']
            )
        ) );
    }

    /**
     * Treatment of pending tasks
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function update_pending_tasks($request) {
        if (! empty($request->get_param('client_id')) && $request->get_param('state') == 'SUCCESS') {
            $posts = $this->WordPress_CustomFunctions->get_posts_by_task_ID($request['client_id']);

            foreach ($posts as $post) {
                $title = '';
                $current_stage = 8;

                $_redaction_io_task_status = get_post_meta($post->ID, '_redaction_io_task_status', true);
                if ($_redaction_io_task_status == 'SUCCESS') {
                    continue;
                }

                $redaction = $this->Redaction_IO_API->get_redaction_by_task_id($request['client_id']);
                $content = $this->WordPress_CustomFunctions->format_text_by_editor($redaction->html, true);

                update_post_meta($post->ID, '_redaction_io_task_total_stage', $current_stage);
                update_post_meta($post->ID, '_redaction_io_task_current_stage', $current_stage);
                update_post_meta($post->ID, '_redaction_io_task_status', 'SUCCESS');

                if (isset($redaction->h1)) {
                    $title = $redaction->h1;
                }

                $seo = $this->WordPress_CustomFunctions->get_fields_name_seo_plugins($redaction->titleseo, $redaction->descriptionseo);
                $this->WordPress_CustomFunctions->update_post_redaction_io($post->ID, $title, $content, $seo, $redaction->slug);

                update_post_meta($post->ID, '_redaction_io_regenerate_slug', false);
            }

            return new WP_REST_Response( '', 200 );
        }

        return new WP_Error( 'cant-update', __( 'Not possible to update this task ID', 'redaction-io' ), array( 'status' => 500 ) );
    }

    /**
     * Set permission for route
     */
    public function permissions_check() {
        return true;
    }
}

new redaction_io_rest_api();