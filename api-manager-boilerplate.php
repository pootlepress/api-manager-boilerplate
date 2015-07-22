<?php
/**
 * Plugin Name: API Manager Boilerplate
 * Plugin URI: http://www.pootlepress.com/
 * Description: This is for quick API manager integration
 * Version: 0.2.5
 * Author: pootlepress
 * Author URI: https://www.pootlepress.com/

 Instructions:
 Copy pp-api folder to the root* of your plugin.
 Put require_once... and new Pootle... lines and their comments description in you main plugin file
 Change API Manager Boilerplate and it's derivatives with your plugin name and token
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Including PootlePress_API_Manager class */
require_once( plugin_dir_path( __FILE__ ) . 'pp-api/class-pp-api-manager.php' );
/** Instantiating PootlePress_API_Manager */
new PootlePress_API_Manager( 'API_Manager_Boilerplate', 'API Manager Boilerplate', '0.2.5', __FILE__, 'api-manager-text-domain', 'http://shramee.thisistap.com/' );