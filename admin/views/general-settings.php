<?php

    if ( ! current_user_can( 'manage_options' ) ) {
        ?>
            <div style="font-size: 20px; margin-top: 20px"> <?php echo esc_html_e( "You don't have access to this page", "icomply" ); ?> </div>
        <?php
        return;
    }

    $cookie_notice_status = ( isset(get_option('enable_cookie_notice')[0]) ? esc_html( get_option('enable_cookie_notice')[0] ) : esc_html( "off" ) );
?>

<div class="wrap">
    <h1><?php esc_html_e('Cookie Notice Settings', 'icomply'); ?> </h1>

    <hr>
    <?php echo settings_errors(); ?>

    <div class="wbl-spacing-20"></div>

    <div class="row">
        <div class="col-md-7 col-left">
            <div class="wbl-section">
            <form method="post" action="options.php">
                <div class="wbl-section-panel wbl-section-top wbl-text-right">
                    <div>
                        <?php if( $cookie_notice_status == "on" ) : ?>
                            <div class="wbl-active-status status-on">
                                <div>
                                    <img src="<?php echo esc_url( WBL_PLUGIN_URL ); ?>public/images/active.png" alt="" />
                                </div>
                                <div>
                                    <?php esc_html_e('Cookie Notice is currently active', 'icomply'); ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="wbl-active-status status-off">
                                <div>
                                    <img src="<?php echo esc_url( WBL_PLUGIN_URL ); ?>public/images/inactive.png" alt="" />
                                </div>
                                <div>
                                    <?php esc_html_e('Cookie Notice is currently inactive', 'icomply'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php submit_button(); ?>
                    </div>
                </div>

                <?php
                    settings_fields( 'smashing_fields' );
                    do_settings_sections( 'smashing_fields' );
                ?>

                <div class="wbl-section-panel wbl-section-bottom">
                    <div>
                        <?php if( $cookie_notice_status == "on" ) : ?>
                            <div class="wbl-active-status status-on">
                                <div>
                                    <img src="<?php echo esc_url( WBL_PLUGIN_URL ); ?>public/images/active.png" alt="" />
                                </div>
                                <div>
                                    <?php esc_html_e('Cookie Notice is currently active', 'icomply'); ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="wbl-active-status status-off">
                                <div>
                                    <img src="<?php echo esc_url( WBL_PLUGIN_URL ); ?>public/images/inactive.png" alt="" />
                                </div>
                                <div>
                                    <?php esc_html_e('Cookie Notice is currently inactive', 'icomply'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php submit_button(); ?>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <div class="col-md-5 col-right">
            <div class="wbl-section wbl-plugin-info">
                <h2>
                    <strong><?php esc_html_e('iComply - Cookie Notice', 'icomply'); ?></strong>
                </h2>
                <hr>

                <div class="plugin-preview-image">
                    <a href="<?php echo esc_url('https://codecanyon.net/item/icomply-cookie-notice-for-wordpress/32016054'); ?>" target="_blank">
                        <img src="<?php echo esc_url( WBL_PLUGIN_URL ); ?>admin/images/preview.jpg" alt="cookie" />
                    </a>
                </div>

                <h2>
                    <strong><?php esc_html_e('Key Features', 'icomply') ?></strong>
                </h2>
                <hr>

                <ul>
                    <li><?php esc_html_e('3 color themes and 4 positions to choose from', 'icomply') ?> </li>
                    <li><?php esc_html_e('Cross browser compatible', 'icomply') ?></li>
                    <li><?php esc_html_e('Smooth show in animation', 'icomply') ?></li>
                    <li><?php esc_html_e('Quick Installation and Simple Configuration', 'icomply') ?></li>
                    <li><?php esc_html_e('Powered by Pure Javascript', 'icomply') ?></li>
                </ul>

                <div class="wbl-cta">
                    <a href="<?php echo esc_url('https://codecanyon.net/item/icomply-cookie-notice-for-wordpress/32016054/comments') ?>" class="tap" target="_blank"><?php esc_html_e('Need help? Ask for support!', 'icomply'); ?></a>

                    <div class="wbl-card">
                        <p class="wbl-text-center">
                            <img src="<?php echo esc_url( WBL_PLUGIN_URL ); ?>admin/images/lucas80.png" alt="" />
                            <br>
                            <strong><?php esc_html_e('Hi, I\'m Lucas from', 'icomply') ?></strong> <a href="<?php echo esc_url('https://weblucas.info/') ?>" target="_blank"><?php echo esc_html('weblucas.info') ?></a>
                        </p>
                        <p>
                            <?php esc_html_e('Thanks for choosing iComply - Cookie Notice!', 'icomply') ?>
                            <a href="<?php echo esc_url('https://codecanyon.net/item/icomply-cookie-notice-for-wordpress/reviews/32016054') ?>" target="_blank"><?php esc_html_e('Please review this plugin', 'icomply'); ?></a>
                            <?php esc_html_e('if you enjoy using it! This help us get better :)', 'icomply') ?>
                        </p>

                        <p>
                           <strong> <?php esc_html_e('Best Regards, Lucas', 'icomply') ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>