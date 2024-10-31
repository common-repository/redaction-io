<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');
?>

<div class="redaction-io_cpt_launch">
    <div class="redaction-io_cpt_launch_wrapper">
        <input type="text" name="keyword_redaction_io" placeholder="<?php _e('Keyword', 'redaction-io'); ?>" value="<?php echo esc_html($_redaction_io_keyword); ?>">
        <select name="lang_keyword_redaction_io">
            <?php foreach ($languages as $name => $lang) { ?>
                <option value="<?php echo esc_html($lang); ?>" <?php echo ($lang == $_redaction_io_lang ? 'selected="selected"' : ''); ?>><?php echo esc_html($name); ?></option>
            <?php } ?>
        </select>
        <button class="redaction-io-btn redaction-io_cpt_launch_submit" type="button">
            <?php  _e('Generate', 'redaction-io'); ?>
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                <path fill="#fff" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                  <animateTransform
                          attributeName="transform"
                          attributeType="XML"
                          type="rotate"
                          dur="1s"
                          from="0 50 50"
                          to="360 50 50"
                          repeatCount="indefinite" />
              </path>
            </svg>
        </button>
    </div>
    <div class="redaction-io_cpt_step --isHidden">
        <div class="redaction-io_cpt_step_progress isHidden">
            <div class="redaction-io_cpt_step_progress_bar">16%</div>
        </div>
        <p class="redaction-io_cpt_step_message"><span></span> <strong></strong></p>
    </div>
</div>