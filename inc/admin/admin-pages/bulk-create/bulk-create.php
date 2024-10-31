<?php
defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');
?>
    <?php if (isset($response['error'])) { ?>
        <div class="redaction-io-notice-save <?php echo ($response['error'] ? '--isError' : ''); ?>">
            <div class="redaction-io-notice-save_wrapper">
                <p><?php echo esc_html($response['message']); ?></p>
                <?php if (! $response['error']) { ?>
                    <p><a href="<?php echo esc_html($response['redirectLink']); ?>"><?php _e('See list of publications', 'redaction-io'); ?></a></p>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <div id="redaction-io-content" class="redaction-io-option">
        <div class="redaction-io-option_settings">
            <div class="redaction-io-box">
                <div class="title">
                    <h3><?php _e('Bulk create', 'redaction-io'); ?></h3>
                </div>
                <div class="inner">
                    <form method="POST">
                        <div class="redaction-io-box_field">
                            <label><?php _e('Generate publications as :', 'redaction-io'); ?></label>
                            <?php
                            $checked = 'checked';
                            foreach ($posts_types as $post_type) { ?>
                                <label class="redaction-io-box_field_labelCheckbox">
                                    <input type="radio" name="redaction_io_bulk_create_post_type" value="<?php echo esc_html($post_type->name); ?>" <?php echo esc_html($checked); ?>>
                                    <span><?php echo esc_html($post_type->label); ?></span>
                                </label>
                                <?php
                                if (! empty($checked)) {
                                    $checked = '';
                                }
                                ?>
                            <?php } ?>
                        </div>
                        <div class="redaction-io-box_field">
                            <label for="redaction_io_bulk_create_lang"><?php _e('Languages :', 'redaction-io'); ?></label>
                            <select id="redaction_io_bulk_create_lang" name="redaction_io_bulk_create_lang">
                                <?php foreach ($languages as $name => $lang) { ?>
                                    <option value="<?php echo esc_html($lang); ?>"><?php echo esc_html($name); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="redaction-io-box_field_dropdowns">
                            <?php
                            $args_kses = wp_kses_allowed_html('post');
                            $args_kses['input'] = ['type' => 1, 'data-term-id' => 1, 'id' => 1, 'value' => 1];
                            $args_kses['svg'] = ['class' => 1, 'aria-hidden' => 1, 'viewBox' => 1];
                            $args_kses['path'] = ['d' => 1];
                            $args_kses['select'] = ['name' => 1, 'multiple' => 1, 'class' => 1, 'data' => 1];
                            $args_kses['option'] = ['value' => 1];
                            echo wp_kses($dropdown, $args_kses); ?>
                        </div>
                        <div class="redaction-io-box_field">
                            <label for="redaction_io_bulk_create_keywords"><?php _e('Keywords (one keyword per line) :', 'redaction-io'); ?></label>
                            <textarea id="redaction_io_bulk_create_keywords" name="redaction_io_bulk_create_keywords" cols="30" rows="10" placeholder="<?php _e("Keyword 1 \r\nKeyword 2 \r\nKeyword 3", 'redaction-io'); ?>" required></textarea>
                        </div>
                        <div class="redaction-io-box_field">
                            <button class="redaction-io-btn"><?php _e('Generate publications', 'redaction-io'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
