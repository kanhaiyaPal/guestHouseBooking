<?php
if ( !defined( 'ABSPATH' ) ) {
	exit();
}
/*
Plugin Name: Guest House Online Booking
Plugin URI: http://www.gapinfotech.com
Description: This plugin provide you an extensive system to manage guest houses at multiple location and multiple cities
Author: Kanhaiya Pal, PHP Developer, Gap Infotech-Gurugram
Version: 0.0.1
Author URI: https://github.com/kanhaiyaPal
*/

define( 'GHOB_PLUGIN', __FILE__ );

define( 'GHOB_PLUGIN_DIR', untrailingslashit( dirname( GHOB_PLUGIN ) ) );


/*include all activation scripts*/
include_once GHOB_PLUGIN_DIR. '/includes/admin/class-ghob-plugin-activation.php';;
register_activation_hook( __FILE__, array( 'GHOIB_plugin_activation', 'create_GHOB_plugin_database_table' ) );

require_once GHOB_PLUGIN_DIR . '/includes/init_plugin.php';
?>