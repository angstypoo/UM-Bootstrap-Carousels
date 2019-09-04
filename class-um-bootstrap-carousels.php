<?php
/**
 * UM Bootstrap Carousels
 *
 * core plugin class
 *
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

if ( ! class_exists( 'Um_Bootstrap_Carousels') ) :

  class Um_Bootstrap_Carousels {

    protected $version;

    public function __construct() {

      $this->version = UM_BOOTSTRAP_CAROUSELS_VERSION;

      $this->load_dependencies();

    }

    /**
     * Retrieve the version
     *
     */
    public function get_version() {

      return $this->version;

    }
    /**
     * Plugin Localization
     *
     *
     **/
    public function localize() {
      load_plugin_textdomain(
  			'um-bootstrap-carousels',
  			false,
  			dirname( __FILE__ ) . '/languages/'
  		);
    }

    /**
     * Load all dependencies
     *
     */
    private function load_dependencies() {

      /**
       * class for admin actions
       */
      require_once dirname( __FILE__ ) . '/admin/class-um-bootstrap-carousels-admin.php';

      /**
       * class for public actions
       */
      require_once dirname( __FILE__ ) . '/public/class-um-bootstrap-carousels-public.php';
    }

    /**
     *
     * Register these scripts for access by both admin and public code
     *
     **/
    public function register_scripts() {

      wp_register_script('umis', plugin_dir_url( __FILE__ ).'dependencies/umis.js', array(), $this->version, true);

      //bootstrap.bundle.min includes popper js
      wp_register_script('um-slider-bootstrap', plugin_dir_url(  __FILE__  ).'dependencies/bootstrap.bundle.min.js', array('jquery'), $this->version, true);

      wp_register_style('um-slider-bootstrap', plugin_dir_url( __FILE__ ).'dependencies/bootstrap.min.css', array(), $this->version, false);

    }

    /**
     * load admin facing code
     *
     * The menu page is only rendered to users with the "manage_options" capability
     *
     * The only contact this plugin has with the database is with the three options it creates - um-bootstrap-carousels, um-bootstrap-carousels-settings, and um-bootstrap-carousel-notices
     *
     */

    private function admin_hooks() {

      $admin = new Um_Bootstrap_Carousels_Admin( $this->get_version() );

      //add the plugin to the menu
      add_action('admin_menu', array( $admin, 'add_to_admin' ) );
      //load global admin actions
      $admin->admin_plugin_global_actions();

    }



    /**
     * load public facing code
     *
     */
    private function public_hooks() {

      $public = new Um_Bootstrap_Carousels_Public( $this->get_version() );

      add_action( 'wp_enqueue_scripts', array($public, 'register_public_scripts'));

      //hook shortcode creation to init action hook
      add_action( 'init', array( $public, 'add_shortcode' ) );

    }

    /**
     * Initialize Plugin
     *
     */
    public function init() {

      add_action( 'plugins_loaded', array($this, 'localize') );

      add_action( 'wp_loaded', array($this,'register_scripts') );

      $this->public_hooks();
      $this->admin_hooks();

    }


  }

endif;
