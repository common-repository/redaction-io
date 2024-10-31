<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

$response_user_api = $this->Redaction_IO_API->get_user();
$redaction_io_apikey = get_option('redaction_io_apikey');
?>

<div id="redaction-io-content" class="redaction-io-option">
    <div class="redaction-io-option_settings">
        <div class="redaction-io-box">
            <div class="title">
                <h3><?php _e('License', 'redaction-io'); ?></h3>
            </div>
            <div class="inner">
                <p><?php _e('Please enter your API key below. If you don\'t have one yet, take a look at our <a href="https://www.redaction.io/" target="_blank">details and prices</a>.', 'redaction-io'); ?></p>
                <form action="options.php" method="POST">
                    <label for="redaction-io_apikey"><?php _e('API key', 'redaction-io'); ?></label>
                    <div class="redaction-io-input-wrap">
                        <input type="text" id="redaction-io_apikey" name="redaction_io_apikey" value="<?php echo esc_html($redaction_io_apikey); ?>">
                    </div>
                    <?php settings_fields( 'redaction_io_option_group' ); ?>
                    <input type="submit" value="<?php _e('Activate your API key', 'redaction-io'); ?>" class="redaction-io-btn">
                </form>
            </div>
        </div>
    </div>
    <?php if (! empty($response_user_api) && ! empty($redaction_io_apikey)) { ?>
        <div class="redaction-io-option_sidebar">
            <div class="redaction-io-box">
                <div class="title">
                    <h3><?php _e('Account information', 'redaction-io'); ?></h3>
                </div>
                <div class="inner">
                    <?php
                    if ($response_user_api->status == 200) { ?>
                        <p><strong><?php _e('Email', 'redaction-io'); ?></strong> : <?php echo esc_html($response_user_api->user_id); ?></p>
                        <p><strong><?php _e('Token(s)', 'redaction-io'); ?></strong> : <?php echo esc_html($response_user_api->tokens); ?></p>
                    <?php } else { ?>
                        <p class="redaction-io-error"><?php echo esc_html($response_user_api->detail); ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php
