<?php
/**
 * Fired during plugin uninstallation
 *
 * Deletes all data stored in the database related to the plugin
 *
 *
 * @link       umethod.net
 *
 * @package    um-bootstrap-carousels
 *
 * @author     Bryce Leue <bryce@umethod.net>
 */

 // If this file is called directly, abort.
 if ( ! defined( 'WPINC' ) ) {
  die;
 }

class Um_Bootstrap_Carousels_Uninstall {

  public static function delete_options() {
    delete_option('um-bootstrap-carousels');
    delete_option('um-bootstrap-carousels-settings');
    delete_option('um-bootstrap-carousels-notices');
  }

}
