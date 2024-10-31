<?php

namespace Redaction_IO\Helpers;

/**
 * Class Redaction_IO API
 *
 */
class Redaction_IO_API
{

    private string $baseurl;
    /**
     * @var false|mixed|void
     */
    private $apikey;
    /**
     * @var int[]
     */
    private array $cost_billed;

    function __construct()
    {
        $this->apikey = get_option('redaction_io_apikey');
        $this->baseurl = 'http://api.redaction.io/api/';
        $this->steps_text = [
            0 => __('Keyword Research in progress...', 'redaction-io'),
            1 => __('Keyword Research in progress...', 'redaction-io'),
            2 => __('Topic Generation in progress...', 'redaction-io'),
            3 => __('Text Generation...', 'redaction-io'),
            4 => __('Title and Meta description...', 'redaction-io'),
            5 => __('Content Optimization...', 'redaction-io'),
            6 => __('Content Optimization...', 'redaction-io'),
            7 => __('Content Optimization...', 'redaction-io'),
            8 => __('Done...', 'redaction-io'),
        ];
        $this->cost_billed = [
            'launch_redaction' => 1,
            'fetch_redaction' => 1
        ];
    }

    /**
     * Get data account (email/tokens)
     *
     * @return int
     */
    public function get_user()
    {
        $args = array(
            'headers' => [
                'Accept'    => 'application/json',
                'API-Key'   => $this->apikey
            ],
        );

        $response   = wp_remote_get( $this->baseurl . 'user/', $args );
        $http_code  = wp_remote_retrieve_response_code( $response );
        $body       = wp_remote_retrieve_body( $response );

        $responseDecode = json_decode($body);
        $responseDecode->status = $http_code;

        return $responseDecode;
    }

    /**
     * Set webhook for user
     *
     * @return void
     */
    public function set_webhook()
    {
        $args = array(
            'body' => json_encode([
                'webhook_url'       => REDACTION_IO_PLUGIN_DIR_URL . 'webhook/completed-task.php',
            ]),
            'headers'   => [
                'Accept'            => 'application/json',
                'Content-Type'      => 'application/json',
                'API-Key'           => $this->apikey
            ],
        );

        $response   = wp_remote_post( $this->baseurl . 'user/webhook/', $args );
        $http_code  = wp_remote_retrieve_response_code( $response );
        $body       = wp_remote_retrieve_body( $response );

        $responseDecode = json_decode($body);
        $responseDecode->status_http = $http_code;

        return $responseDecode;
    }

    /**
     * Set new redaction by keyword
     *
     * @param $keyword
     * @param $lang
     * @return mixed
     */
    public function set_new_redaction_by_keyword($keyword, $lang)
    {
        $args = array(
            'body' => json_encode([
                'target_keyword'    => $keyword,
                'metaprompt_name'   => 'meta/article_complet',
                'lang'              => $lang,
                'webhook_url'       => get_home_url() . '/' . rest_get_url_prefix() . '/redaction-io/v1/completed-task'
            ]),
            'headers'   => [
                'Accept'            => 'application/json',
                'Content-Type'      => 'application/json',
                'API-Key'           => $this->apikey
            ],
        );

        $response   = wp_remote_post( $this->baseurl . 'task/redaction/', $args );
        $http_code  = wp_remote_retrieve_response_code( $response );
        $body       = wp_remote_retrieve_body( $response );

        $responseDecode = json_decode($body);
        $responseDecode->status_http = $http_code;

        return $responseDecode;
    }

    /**
     * Get redaction data by task ID
     *
     * @param $task_id
     * @return false|mixed
     */
    public function get_redaction_by_task_id($task_id)
    {
        if (empty($task_id)) {
            return false;
        }

        $args = array(
            'headers'   => [
                'Accept'            => 'application/json',
                'Content-Type'      => 'application/json',
                'API-Key'           => $this->apikey
            ],
        );

        $response   = wp_remote_get( $this->baseurl . 'task/redaction/' . $task_id, $args );
        $http_code  = wp_remote_retrieve_response_code( $response );
        $body       = wp_remote_retrieve_body( $response );

        $responseDecode = json_decode($body);
        $responseDecode->status_http = $http_code;

        return $responseDecode;
    }

    /**
     * Verify remaining tokens by account
     *
     * @param $task
     * @param $count
     * @return bool
     */
    public function check_remaining_tokens($task, $count)
    {
        $user = $this->get_user();
        $tokens = 0;

        if (isset($user->tokens)) {
            $tokens = $user->tokens;
        }

        if ($tokens >= ($this->cost_billed[$task] * $count)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get all languages managed by the API
     *
     * @return string[]
     */
    public function get_languages()
    {
        return [
            __('French', 'redaction-io') => 'fr-FR',
            __('English', 'redaction-io') => 'en-GB',
            __('Italian', 'redaction-io') => 'it',
            __('Portuguese', 'redaction-io') => 'pt',
            __('Brazilian', 'redaction-io') => 'pt-BR',
            __('Spanish', 'redaction-io') => 'es',
            __('German', 'redaction-io') => 'de',
            __('Dutch', 'redaction-io') => 'nl',
        ];
    }
}
