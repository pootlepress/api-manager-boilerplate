<?php
/**
 * Plugin Name: API Manager Boilerplate
 * Plugin URI: http://www.pootlepress.com/
 * Description: API manager plugin boilerplate
 * Version: 0.1.2
 * Author: pootlepress
 * Author URI: https://www.pootlepress.com/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class API_Manager_Example {

	/** @var string Base URL to the remote upgrade API Manager server */
	public $upgrade_url = 'http://shramee.thisistap.com/';

	/** @var string Version */
	public $version = '0.1.2';

	/** @var string Token for this plugin */
	public $token = 'api_manager_example';

	/** @var string Plugin name */
	public $name = 'API Manager Boilerplate';

	/** @var string */
	public $plugin_url;

	/** @var string Plugin textdomain */
	public $text_domain;

	/**
	 * Data defaults
	 * @var mixed
	 */
	private $software_product_id;

	public $data_key;
	public $api_key;
	public $activation_email;
	public $instance_key;
	public $deactivate_checkbox_key;
	public $activated_key;

	public $deactivate_checkbox;
	public $activation_tab_key;
	public $deactivation_tab_key;
	public $settings_menu_title;
	public $settings_title;
	public $menu_tab_activation_title;
	public $menu_tab_deactivation_title;

	public $options;
	public $plugin_name;
	public $product_id;
	public $renew_license_url;
	public $instance_id;
	public $domain;
	public $software_version;
	public $plugin_or_theme;

	public $update_version;

	/**
	 * Used to send any extra information.
	 * @var mixed array, object, string, etc.
	 */
	public $extra;

    /**
     * @var The single instance of the class
     */
    protected static $_instance = null;

    public static function instance() {

        if ( is_null( self::$_instance ) ) {
        	self::$_instance = new self();
        }

        return self::$_instance;
    }

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.2
	 */
	private function __clone() {}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.2
	 */
	private function __wakeup() {}

	public function __construct() {

		/**
		 * Displays an inactive message if the API License Key has not yet been activated
		 */
		if ( get_option( $this->token . '_activated' ) != 'Activated' ) {
			add_action( 'admin_notices', array( $this, 'inactive_notice' ) );
		}

		// Run the activation function
		register_activation_hook( __FILE__, array( $this, 'activation' ) );

		if ( is_admin() ) {

			// Check for external connection blocking
			add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );

			/**
			 * Software Product ID is the product title string
			 * This value must be unique, and it must match the API tab for the product in WooCommerce
			 */
			$this->software_product_id = $this->name;

			/**
			 * Set all data defaults here
			 */
			$this->data_key 				= $this->token;
			$this->api_key 					= 'api_key';
			$this->activation_email 		= 'activation_email';
			$this->instance_key 			= $this->token . '_instance';
			$this->deactivate_checkbox_key 	= $this->token . '_deactivate_checkbox';
			$this->activated_key 			= $this->token . '_activated';

			/**
			 * Set all admin menu data
			 */
			$this->deactivate_checkbox 			= 'am_deactivate_example_checkbox';
			$this->activation_tab_key 			= $this->token . '_dashboard';
			$this->deactivation_tab_key 		= $this->token . '_deactivation';
			$this->settings_menu_title 			= $this->name;
			$this->settings_title 				= $this->name;
			$this->menu_tab_activation_title 	= __( 'License Activation', 'api-manager-example' );
			$this->menu_tab_deactivation_title 	= __( 'License Deactivation', 'api-manager-example' );

			/**
			 * Set all software update data here
			 */
			$this->options 				= get_option( $this->data_key );
			$this->plugin_name 			= untrailingslashit( plugin_basename( __FILE__ ) ); // same as plugin slug. if a theme use a theme name like 'twentyeleven'
			$this->product_id 			= get_option( $this->token . '_product_id' ); // Software Title
			$this->renew_license_url 	= 'http://localhost/toddlahman/my-account'; // URL to renew a license. Trailing slash in the upgrade_url is required.
			$this->instance_id 			= get_option( $this->instance_key ); // Instance ID (unique to each blog activation)
			/**
			 * Some web hosts have security policies that block the : (colon) and // (slashes) in http://,
			 * so only the host portion of the URL can be sent. For example the host portion might be
			 * www.example.com or example.com. http://www.example.com includes the scheme http,
			 * and the host www.example.com.
			 * Sending only the host also eliminates issues when a client site changes from http to https,
			 * but their activation still uses the original scheme.
			 * To send only the host, use a line like the one below:
			 *
			 * $this->domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
			 */
			$this->domain 				= str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
			$this->software_version 	= $this->version; // The software version
			$this->plugin_or_theme 		= 'plugin'; // 'theme' or 'plugin'

			// Performs activations and deactivations of API License Keys
			require_once( plugin_dir_path( __FILE__ ) . 'am/classes/class-wc-key-api.php' );

			// Checks for software updatess
			require_once( plugin_dir_path( __FILE__ ) . 'am/classes/class-wc-plugin-update.php' );

			// Admin menu with the license key and license email form
			require_once( plugin_dir_path( __FILE__ ) . 'am/admin/class-wc-api-manager-menu.php' );

			$options = get_option( $this->data_key );

			/**
			 * Check for software updates
			 */
			if ( ! empty( $options ) && $options !== false ) {

				$this->update_check(
					$this->upgrade_url,
					$this->plugin_name,
					$this->product_id,
					$this->options['api_key'],
					$this->options['activation_email'],
					$this->renew_license_url,
					$this->instance_id,
					$this->domain,
					$this->software_version,
					$this->plugin_or_theme,
					$this->text_domain
					);

			}

		}

		/**
		 * Deletes all data if plugin deactivated
		 */
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );

	}

	/** Load Shared Classes as on-demand Instances **********************************************/

	/**
	 * API Key Class.
	 *
	 * @return Api_Manager_Example_Key
	 */
	public function key() {
		return Api_Manager_Example_Key::instance();
	}

	/**
	 * Update Check Class.
	 *
	 * @return API_Manager_Example_Update_API_Check
	 */
	public function update_check( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra = '' ) {

		return API_Manager_Example_Update_API_Check::instance( $upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, $software_version, $plugin_or_theme, $text_domain, $extra );
	}

	public function plugin_url() {
		if ( isset( $this->plugin_url ) ) {
			return $this->plugin_url;
		}

		return $this->plugin_url = plugins_url( '/', __FILE__ );
	}

	/**
	 * Generate the default data arrays
	 */
	public function activation() {
		global $wpdb;

		$global_options = array(
			'api_key' 				=> '',
			'activation_email' 	=> '',
					);

		update_option( $this->data_key, $global_options );

		// Generate a unique installation $instance id
		$instance = $this->generate_password();

		$single_options = array(
			$this->token . '_product_id' 			=> $this->software_product_id,
			$this->instance_key 			=> $instance,
			$this->deactivate_checkbox_key 	=> 'on',
			$this->activated_key 			=> 'Deactivated',
			);

		foreach ( $single_options as $key => $value ) {
			update_option( $key, $value );
		}

		$curr_ver = get_option( 'pootle_' . $this->token . '_version' );

		// checks if the current plugin version is lower than the version being installed
		if ( version_compare( $this->version, $curr_ver, '>' ) ) {
			// update the version
			update_option( 'pootle_' . $this->token . '_version', $this->version );
		}

	}

	/**
	 * Deletes all data if plugin deactivated
	 * @return void
	 */
	public function uninstall() {
		global $wpdb, $blog_id;

		$this->license_key_deactivation();

		// Remove options
		if ( is_multisite() ) {

			switch_to_blog( $blog_id );

			foreach ( array(
					$this->data_key,
					$this->token . '_product_id',
					$this->instance_key,
					$this->deactivate_checkbox_key,
					$this->activated_key,
					) as $option) {

					delete_option( $option );

					}

			restore_current_blog();

		} else {

			foreach ( array(
					$this->data_key,
					$this->token . '_product_id',
					$this->instance_key,
					$this->deactivate_checkbox_key,
					$this->activated_key
					) as $option) {

					delete_option( $option );

					}

		}

	}

	/**
	 * Deactivates the license on the API server
	 * @return void
	 */
	public function license_key_deactivation() {

		$activation_status = get_option( $this->activated_key );

		$api_email = $this->options['activation_email'];
		$api_key = $this->options['api_key'];

		$args = array(
			'email' => $api_email,
			'licence_key' => $api_key,
			);

		if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
			$this->key()->deactivate( $args ); // reset license key activation
		}
	}

    /**
     * Displays an inactive notice when the software is inactive.
     */
	public function inactive_notice() { ?>
		<?php if ( ! current_user_can( 'manage_options' ) ) return; ?>
		<?php if ( isset( $_GET['page'] ) && 'api_manager_example_dashboard' == $_GET['page'] ) return; ?>
		<div id="message" class="error">
			<p><?php printf( __( $this->name . ' API License Key has not been activated, so the plugin is inactive! %sClick here%s to activate the license key and the plugin.', 'api-manager-example' ), '<a href="' . esc_url( admin_url( 'options-general.php?page=api_manager_example_dashboard' ) ) . '">', '</a>' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Check for external blocking contstant
	 * @return string
	 */
	public function check_external_blocking() {
		// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
		if( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

			// check if our API endpoint is in the allowed hosts
			$host = parse_url( $this->upgrade_url, PHP_URL_HOST );

			if( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
				?>
				<div class="error">
					<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', 'api-manager-example' ), $this->software_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>'); ?></p>
				</div>
				<?php
			}

		}
	}

	/**
	 * Creates a random instance ID
	 * @param int $length
	 * @return string Random alphanumeric string
	 */
	public function generate_password( $length = 12 ) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		$password = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$password .= substr( $chars, rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		return $password;
	}

} // End of class

function AME() {
    return API_Manager_Example::instance();
}

// Initialize the class instance only once
AME();
