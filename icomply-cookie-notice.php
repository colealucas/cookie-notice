<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the admin area.
 * This file includes all of the dependencies used by the plugin.
 *
 * @package           icomply
 *
 * Plugin Name:       iComply Cookie Notice
 * Description:       The simplest way to show your website complies with the EU Cookie Law.
 * Version:           1.0.1
 * Requires at least: 4.6
 * Requires PHP:      5.6
 * Author:            Nicu Lucas
 * Author URI:        https://weblucas.info
 * Text Domain:       icomply
 * Domain Path:       /languages
 * License:           GPLv3
 */

defined( 'ABSPATH' ) or die( 'access denied!' );

define ( 'WBL_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define ( 'WBL_PLUGIN_NAME', plugin_basename( __FILE__ ));


class IComplyCookieNotice{
    function __construct()
    {
        if( function_exists( 'add_action' ) )
        {
            // admin assets
            add_action( 'admin_enqueue_scripts', array($this, 'load_admin_assets'));

            // front-end assets
            add_action('wp_enqueue_scripts', array($this, 'load_assets'));

            // create settings
            add_action( 'admin_init', array($this, 'setup_fields') );

            // add new page to admin menu
            add_action( 'admin_menu', array($this, 'add_plugin_settings_page') );

            // add link to settings page
            $plugin_name = 'plugin_action_links_' . WBL_PLUGIN_NAME;
            add_filter( $plugin_name, array($this, 'settings_link') );

            // add cookie notice in footer
            add_action('wp_footer', array($this, 'display_cookie_notice'));

            // set locale
           add_action( 'plugins_loaded', array($this, 'load_plugin_textdomain'));
        }
    }

    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'icomply',
            false,
            basename( dirname( __FILE__ ) ) . '/languages/'
        );
    }

    function settings_link( $links )
    {
        $settings_link  = '<a href="'.esc_url( 'admin.php?page=icomply_cookie_notice' ).'">'.esc_html__('Settings', 'icomply').' </a>';
        $review_link    = '<a href="'.esc_url( 'https://codecanyon.net/item/icomply-cookie-notice-for-wordpress/reviews/32016054' ).'" target="_blank">'.esc_html__('Rate us!', 'icomply').'</a>';
        $support_link   = '<a href="'.esc_url( 'https://codecanyon.net/item/icomply-cookie-notice-for-wordpress/32016054/comments' ).'" target="_blank" style="color: #82B440"><b>'.esc_html__('Get Support', 'icomply').'</b></a>';

        array_push($links, $settings_link);
        array_push($links, $support_link);
        array_push($links, $review_link);
        return $links;
    }

    function load_admin_assets()
    {
        if( isset($_GET['page']) and $_GET['page'] == "icomply_cookie_notice" ){
            // media upload
            if(function_exists( 'wp_enqueue_media' )){
                wp_enqueue_media();
            }
            else{
                wp_enqueue_style('thickbox');
                wp_enqueue_script('media-upload');
                wp_enqueue_script('thickbox');
            }

            // custom scripts and styles
            wp_enqueue_style( 'i-comply-styles', WBL_PLUGIN_URL . 'admin/css/icomply.css', array(), 1.0, 'all' );
            wp_register_script('i-comply-script', WBL_PLUGIN_URL . 'admin/js/icomply.js', array('jquery', 'media-upload', 'thickbox'));
            wp_enqueue_script('i-comply-script');
        }
    }

    function load_assets()
    {
        if( !isset( $_COOKIE['icomply-cookie-accepted'] ) ){
            wp_enqueue_style( 'i-comply', WBL_PLUGIN_URL . 'public/css/icomply-styles.css', array(), 1.0, 'all' );
            wp_enqueue_script( 'i-comply', WBL_PLUGIN_URL . 'public/js/icomply-scripts.min.js' , array(), 1.0, true );
        }
    }

    function add_plugin_settings_page()
    {
        $menu_page_args = array(
                'page_title' => esc_html__('iComply Cookie Notice', 'icomply'),
                'menu_title' => esc_html__('Cookie Notice', 'icomply'),
                'capability' => 'manage_options',
                'menu_slug'  => 'icomply_cookie_notice',
                'callback'   =>  array($this, 'settings_page_content'),
                'icon_url'   => 'dashicons-lock',
                'position'   => 26,
        );

       add_menu_page($menu_page_args['page_title'], $menu_page_args['menu_title'], $menu_page_args['capability'], $menu_page_args['menu_slug'], $menu_page_args['callback'], $menu_page_args['icon_url'], $menu_page_args['position'] );
    }

    function section_callback($arguments){
        switch( $arguments['id'] ){
            case 'first_section':
                esc_html_e( 'Enable Cookie Notice then choose color and position options.', 'icomply');
                break;
            case 'second_section':
                esc_html_e('Customize your cookie notice message.', 'icomply');
                break;
            case 'third_section':
                esc_html_e('Set the title and links for your buttons.', 'icomply');
                break;
			case 'fourth_section':
                esc_html_e('Other settings.', 'icomply');
                break;
        }
    }

    function field_callback( $arguments ) {
        $value = get_option( $arguments['uid'] );

        if( is_string( $value ) ){
            $value = wp_kses_post( get_option($arguments['uid']) );
        }
		
		if( !$value ) {
			$value = esc_html( $arguments['default'] );
		}

        switch( $arguments['type'] ){
            case 'text':
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                break;
				
            case 'textarea':
                printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                break;

            case 'editor':
                wp_editor($value, $arguments['uid'], $settings = array('textarea_name' => $arguments['uid'], 'textarea_rows' => 8, 'media_buttons' => false));
                break;
				
            case 'select':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = "";
                    foreach( $arguments['options'] as $key => $label ){
                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value, $key, false ), $label );
                    }
                    printf( '<select name="%1$s" id="%1$s">%2$s</select>', $arguments['uid'], $options_markup );
                }
                break;
				
			case 'radio':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
						$separator = ( !empty($arguments['inline']) ? "&nbsp;&nbsp;" : "<br>" );
                        $options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label>' . $separator, $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
				
			case 'media-image' :
				?>

				<div class="wbl-media-upload flex middle">
					<div>
						<a href="#" class="button wbl-media-upload-button"><?php esc_attr_e('Upload', 'icomply'); ?></a>
						<a href="#" class="button wbl-clear-media"><?php esc_attr_e('Clear', 'icomply'); ?></a>
						
						<?php 
				printf( '<input class="wbl-media-url" name="%1$s" id="%1$s" type="hidden" placeholder="%2$s" value="%3$s" />', $arguments['uid'], $arguments['placeholder'], $value );
						?>
					</div>
					<div>
						<img class="wbl-media-preview" src="<?php echo esc_attr( $value ); ?>" alt="" />
					</div>
				</div> 

				<?php
				break;
			case 'button' :
				?>

				<div class="wbl-button">
					<a href="#" class="button wbl-delete-all-cookie"><?php echo esc_html( $arguments['title'], 'icomply'); ?></a>
				</div> 

				<?php
				break;
        }

        if( !empty($arguments['helper']) ){
            printf( '<span class="helper"> %s</span>', esc_html( $arguments['helper'], 'icomply' ) );
        }

        if( !empty($arguments['supplemental']) ){
            printf( '<p class="description">%s</p>', esc_html( $arguments['supplemental'], 'icomply' ) );
        }
    }

    function setup_fields(){
        add_settings_section( 'first_section', esc_html__('General Settings', 'icomply'), array( $this, 'section_callback' ), 'smashing_fields' );
        add_settings_section( 'second_section', esc_html__('Customize Message', 'icomply'), array( $this, 'section_callback' ), 'smashing_fields' );
        add_settings_section( 'third_section', esc_html__('Customize Buttons', 'icomply'), array( $this, 'section_callback' ), 'smashing_fields' );
		add_settings_section( 'fourth_section', esc_html__('Advanced Settings', 'icomply'), array( $this, 'section_callback' ), 'smashing_fields' );

        $fields = array(
			// section 1
            array(
                'uid' => esc_html('enable_cookie_notice','icomply'),
                'label' => esc_html__('Enable Cookie Notice:', 'icomply'),
                'section' => esc_html('first_section','icomply'),
                'type' => esc_html('radio','icomply'),
                'options' => array(
        			'on' => esc_html__('ON', 'icomply'),
        			'off' => esc_html__('OFF', 'icomply'),
        		),
                'placeholder' => '',
                'helper' => '',
                'supplemental' => esc_html__('Enable/Disable Cookie Notice.', 'icomply'),
                'default' => array( esc_html('off') )
            ),
            array(
                'uid' => esc_html('cookie_notice_position','icomply'),
                'label' => esc_html__('Position:', 'icomply'),
                'section' => esc_html('first_section','icomply'),
                'type' => esc_html('radio','icomply'),
                'options' => array(
        			'top' => esc_html__('Top', 'icomply'),
                    'left' => esc_html__('Left', 'icomply'),
                    'bottom' => esc_html__('Bottom', 'icomply'),
        			'right' => esc_html__('Right', 'icomply'),
        		),
                'placeholder' => '',
                'helper' => '',
                'supplemental' => esc_html__('Choose Cookie Bar Position.', 'icomply'),
				'inline' => true,
                'default' => array( esc_html('bottom') )
            ),
			array(
                'uid' =>  esc_html('cookie_notice_color','icomply'),
                'label' => esc_html__('Color Theme:', 'icomply'),
                'section' =>  esc_html('first_section','icomply'),
                'type' =>  esc_html('radio','icomply'),
                'options' => array(
        			'default' => esc_html__('Default (Light Brown)', 'icomply'),
        			'light' => esc_html__('Light', 'icomply'),
					'dark' => esc_html__('Dark', 'icomply'),
        		),
                'placeholder' => '',
                'helper' => '',
                'supplemental' => esc_html__('Choose Cookie Bar Color Theme.', 'icomply'),
				'inline' => true,
                'default' => array( esc_html('light') )
            ),
			
			// section 2
			array(
                'uid' => esc_html('cookie_notice_message','icomply'),
                'label' => esc_html__('Message:', 'icomply'),
                'section' => esc_html('second_section','icomply'),
                'type' => esc_html('editor','icomply'),
                'options' => false,
                'placeholder' => '',
                'helper' => '',
                'supplemental' => esc_html__('Add your cookie notice message.', 'icomply'),
				'inline' => false,
                'default' => esc_html__('We use cookies to ensure that we give you the best experience on our website. By continuing to use our site, you accept our cookie policy Terms.', 'icomply')
            ),
            array(
                'uid' => esc_html('cookie_notice_show_logo','icomply'),
                'label' => esc_html__('Cookie Image:', 'icomply'),
                'section' => esc_html('second_section','icomply'),
                'type' => esc_html('radio','icomply'),
                'options' => array(
                    'show' => esc_html__('SHOW', 'icomply'),
                    'hide' => esc_html__('HIDE', 'icomply'),
                ),
                'placeholder' => '',
                'helper' => '',
                'inline' => true,
                'supplemental' => esc_html__('Show/Hide Cookie Image.', 'icomply'),
                'default' => array( esc_html('hide') )
            ),
			
			// section 3
            array(
                'uid' => esc_html('cookie_notice_privacy_button_text','icomply'),
                'label' => esc_html__('Privacy Button Text:', 'icomply'),
                'section' => esc_html('third_section','icomply'),
                'type' => esc_html('text','icomply'),
                'options' => false,
                'placeholder' => esc_html__('Privacy Button Text', 'icomply'),
                'helper' => '',
                'supplemental' => esc_html__('Leave empty to hide this button on the front-end.', 'icomply'),
                'inline' => false,
                'default' => esc_html('Privacy Policy', 'icomply'),
            ),
			array(
                'uid' => esc_html('cookie_notice_privacy_button_link','icomply'),
                'label' => esc_html__('Privacy Button Link:', 'icomply'),
                'section' => esc_html('third_section','icomply'),
                'type' => esc_html('text','icomply'),
                'options' => false,
                'placeholder' => esc_html__('https://', 'icomply'),
                'helper' => '',
                'supplemental' => esc_html__('Add link to your privacy policy page.', 'icomply'),
				'inline' => false,
                'default' => '',
            ),
            array(
                'uid' => esc_html('cookie_notice_accept_button_text', 'icomply'),
                'label' => esc_html__('Accept Button Text:', 'icomply'),
                'section' => esc_html('third_section', 'icomply'),
                'type' => esc_html('text', 'icomply'),
                'options' => false,
                'placeholder' => esc_html__('Accept Button Text', 'icomply'),
                'helper' => '',
                'supplemental' => esc_html__('Accept Button Text (Clicking this button will hide the cookie bar).', 'icomply'),
                'inline' => false,
                'default' => esc_html('Accept Cookies', 'icomply'),
            ),
			
			// section 4
			array(
                'uid' => esc_html('cookie_notice_delete_all', 'icomply'),
                'label' => esc_html__('Delete iComply Cookies:', 'icomply'),
				'title' => esc_html__('Delete Plugin Cookies', 'icomply'),
                'section' => esc_html('fourth_section', 'icomply'),
                'type' => esc_html('button', 'icomply'),
                'options' => false,
                'placeholder' => '',
                'helper' => '',
                'supplemental' => esc_html__('Remove all Cookies. (The cookie bar will show again.)', 'icomply'),
				'inline' => false,
                'default' => '',
            ),
          
        );

        foreach( $fields as $field ){
            add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'smashing_fields', $field['section'], $field );
            register_setting( 'smashing_fields', $field['uid'] );
        }
    }

    function settings_page_content()
    {
        require_once plugin_dir_path( __FILE__ ) . 'admin/views/general-settings.php';
    }

    function display_cookie_notice()
    {
        $cookie_notice_status = ( isset( get_option('enable_cookie_notice')[0] ) ? esc_html( get_option('enable_cookie_notice')[0] ) : esc_html( "off" ) );

        if( $cookie_notice_status == "on" and !isset( $_COOKIE['icomply-cookie-accepted'] ) ) :
            $classes_string = "";

            $color             = ( !empty(get_option('cookie_notice_color')[0]) ? esc_html( get_option('cookie_notice_color')[0] ) : esc_html( "light" ) );
            $position          = ( !empty(get_option('cookie_notice_position')[0]) ? esc_html( get_option('cookie_notice_position')[0] ) : esc_html( "bottom") );
            $animated          = ( !empty(get_option('cookie_notice_animated')[0]) ? esc_html( get_option('cookie_notice_animated')[0] ) : esc_html( "animated") );
            $full_width        = ( !empty(get_option('cookie_notice_full_width')[0]) ? esc_html( get_option('cookie_notice_full_width')[0] ) : esc_html( "fw") );

            $message           = ( !empty(get_option('cookie_notice_message')) ? wp_kses_post( get_option('cookie_notice_message') ) : esc_html( "We use cookies to ensure that we give you the best experience on our website. By continuing to use our site, you accept our cookie policy Terms.") );
            $show_cookie_image = ( !empty(get_option('cookie_notice_show_logo')[0]) ? esc_html( get_option('cookie_notice_show_logo')[0] ) : esc_html( "show") );
            $accept_btn_text   = ( !empty(get_option('cookie_notice_accept_button_text')) ? esc_html( get_option('cookie_notice_accept_button_text') ) : esc_html( "Allow") );
            $privacy_btn_text  = ( !empty(get_option('cookie_notice_privacy_button_text')) ? esc_html( get_option('cookie_notice_privacy_button_text') ) : "" );
            $privacy_btn_link  = ( !empty(get_option('cookie_notice_privacy_button_link')) ? esc_url( get_option('cookie_notice_privacy_button_link') ) : "" );

            $classes_string .= "$position $color $animated $full_width";

            ?>
            <div id="simple-cookies" class="simple-cookies <?php echo esc_attr( $classes_string ); ?> ">
                <div class="simple-cookies-wrapper">
                    <div class="simple-cookie-content">
                        <?php if( $show_cookie_image == "show") : ?>
                            <div>
                                <img src="<?php echo esc_url( WBL_PLUGIN_URL ); ?>public/images/cookie.png" alt="cookie" />
                            </div>
                        <?php endif; ?>
                        <div>
                            <?php echo wp_kses_post( $message ); ?>
                        </div>
                    </div>
                    <div class="simple-cookie-buttons">
                        <?php if( !empty($privacy_btn_text) ) : ?>
                            <div>
                                <a href="<?php echo esc_url( $privacy_btn_link ); ?>" class="touch allow" target="_blank"><?php echo esc_html( $privacy_btn_text ); ?></a>
                            </div>
                        <?php endif; ?>
                        <div>
                            <a href="#" class="touch decline js-close"><?php echo esc_html( $accept_btn_text ); ?></a>
                        </div>
                        <div>
                            <span class="sc-closer"><img src="<?php echo esc_url( WBL_PLUGIN_URL ); ?>public/images/closer.png" alt="closer"></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php
       endif;
    }
}

$i_comply_plugin = new IComplyCookieNotice;
