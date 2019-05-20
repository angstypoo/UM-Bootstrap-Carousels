<?php

/**
 *
 * Initialize the plugin
 *
 * @link       umethod.net
 * @package    um-bootstrap-carousels
 * @author     Bryce Leue <bryce@umethod.net>
 *
 * @wordpress-plugin
 * Plugin Name:       UM Bootstrap Carousels
 * Description:       Create and Manage Bootstrap 4 Carousels
 * Version:           1.0.2
 * Author:            Bryce Leue
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       um-bootstrap-carousels
 * Domain Path:       /languages/
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
 die;
}

//version control handled here
define( 'UM_BOOTSTRAP_CAROUSELS_VERSION', '1.0.2');

//register activation hook
function um_bootstrap_carousels_activate() {
  require_once dirname( __FILE__ ).'/um-bootstrap-carousels-activate.php';
  Um_Bootstrap_Carousels_Activate::create_options();
}
register_activation_hook(__FILE__, 'um_bootstrap_carousels_activate');

//register uninstallation code
function um_bootstrap_carousels_uninstall() {
  require_once dirname( __FILE__ ).'/um-bootstrap-carousels-uninstall.php';
  Um_Bootstrap_Carousels_Uninstall::delete_options();
}
register_uninstall_hook( __FILE__, 'um_bootstrap_carousels_uninstall' );

//include the core plugin class
require dirname( __FILE__ ) . '/class-um-bootstrap-carousels.php';


//wrap init with public function
function um_bootstrap_carousels() {
  $um_bootstrap_carousels = new Um_Bootstrap_Carousels();
  $um_bootstrap_carousels->init();

}

//initialize
um_bootstrap_carousels();
