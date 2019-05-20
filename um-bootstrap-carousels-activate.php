<?php
/**
 * Fired during plugin activation
 *
 * Creates all options necessary for plugin operation
 *
 * If plugin has already been activated without being uninstalled, this will not attempt to recreate the options, as they should still exist
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

class Um_Bootstrap_Carousels_Activate {

  public static function create_options() {
    if(!get_option('um-bootstrap-carousels'))  {
      $slider_array = base64_encode(serialize(array()));
      add_option('um-bootstrap-carousels', $slider_array);
    }


    if(!get_option('um-bootstrap-carousels-settings')) {
      $settings_array = base64_encode(serialize(array(
        'bootstrap' => array(
          'version' => '4.3'
        ),
        'styles' => array(
          0 => 'none',
          1 => 'basic'
        )
      )));
      add_option('um-bootstrap-carousels-settings', $settings_array);
    }

    if(!get_option('um-bootstrap-carousels-notices')) {
      $notices_array = base64_encode(serialize(array()));
      add_option('um-bootstrap-carousels-notices', $notices_array );
    }
  }

}
