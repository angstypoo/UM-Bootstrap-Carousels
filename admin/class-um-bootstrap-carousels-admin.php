<?php

/**
 * UM Bootstrap Carousels
 *
 * Admin Plugin Class
 *
 *
 * @package um-bootstrap-carousels
 *
 *
 *  Slider array verbose reference
 *
 * array(
 *
 *  'sliderid' => array(
 *    'title' => 'string',
 *    $itemid => array(
 *      'title' => 'string',
 *      'attachmentid' => 'string',
 *      'pointerurl' => 'string',
 *      'pointertext' => 'string',
 *      'details' => 'string'
 *    ),
 *    'settings' => array (
 *      'autobootstrap' => int, (default 0)
 *      'style' => int, (default 0)
 *    )
 *
 *  )
 *
 * );
 */

class Um_Bootstrap_Carousels_Admin {

  private $version;

  public function __construct( $version ) {
    $this->version = $version;
  }

  /**
   * Admin script enqueuing
   *
   **/

  public function enqueue() {
    wp_enqueue_media();
    wp_enqueue_editor();
    wp_enqueue_script('umis');
    wp_enqueue_script('um-slider-admin', plugin_dir_url( __FILE__ ).'display/scripts/script.js', array('jquery'), $this->version, true);
    wp_enqueue_style('um-slider-admin', plugin_dir_url( __FILE__ ).'display/style.css', array(), $this->version, false);

    //ensure bootstrap is loaded for the admin page
    wp_enqueue_script('um-slider-bootstrap');
    wp_enqueue_style('um-slider-bootstrap');
  }

  /**
   * Query vars for the plugin
   *
   * umbc: for determining selected carousel
   * umbcnotice: for admin passing notice reference
   *
   **/
  public function create_query_vars() {
		global $wp;
		$wp->add_query_var('umbc');
    $wp->add_query_var('umbcnotice');
	}

  /**
   *
   * Get the slider/carousel array
   *
   **/
  private function get_sliders() {

    $sliders = get_option('um-bootstrap-carousels');
    if(!$sliders) return false;

    $sliders = $this->unserialize_decode($sliders);

    return $sliders;

  }

  //wrapper for base64 encode and serialize
  public function encode_serialize($raw) {
    $encoded = base64_encode(serialize($raw));
    return $encoded;
  }

  //wrapper for unserialize and base64 decode
  public function unserialize_decode($encodedstring) {
    $decoded = unserialize(base64_decode($encodedstring));
    return $decoded;
  }

  /**
   * Admin Notices
   *
   * Makes use of GET variables and wp option storage (no session)
   *
   * Creates new notice reference as get var using query var umbcnotice
   * returns urlencoded string in get var to be appended to redirect
   * stores reference to notice in um-bootstrap-carousels-notices option array along with message
   * notice reference has three parts for identification:
   * 1. timestamp
   * 2. type
   * 3. username
   *
   * The first part of the string will be a url numeric time stamp
   * The second part of the get var string will be a "t" followed by a single character reference to type, either "s" or "e", for success or error respectively
   * the third part of the string will be the active user's username
   *
   * default type: 's' (success)
   **/
  public function create_notice($message, $type='s') {
    if($type!=='s' && $type !=='e') return '';
    if(!is_string($message)) return '';

    $username = wp_get_current_user()->user_login;
    $timestamp = time();
    $string=urlencode($timestamp.'t'.$type.$username);

    //store reference to string in notices option
    $notices_array = get_option('um-bootstrap-carousels-notices');
    $notices_array = $this->unserialize_decode($notices_array);
    $message = wp_kses_post($message);
    $notices_array[]=array(
      'reference' => $string,
      'message' => $message
    );
    $notices_array = $this->encode_serialize($notices_array);
    update_option('um-bootstrap-carousels-notices', $notices_array);


    //append string to get var and return;
    return '&umbcnotice='.$string;
  }

  public function parse_notice($n) {
    $notice = urldecode($n);
    $arr = explode('t', $notice, 2);
    $timestamp = intval($arr[0]);
    $type = $arr[1][0];
    $username = substr($arr[1], 1);
    return array(
      'username' => $username,
      'timestamp' => $timestamp,
      'type' => $type
    );
  }

  public function get_notice() {
    //basic validation
    if(!isset($_GET['umbcnotice'])) return 0;
    if(!is_string($_GET['umbcnotice'])) return 0;

    //check to see if notice still exists and delete notices older than 3 minutes
    //avoids collision with other notices by checking matching urlencoded string, which need a combination of the same time(), notice type, and username
    $notices = get_option('um-bootstrap-carousels-notices');
    $notices = $this->unserialize_decode($notices);

    //if no notices exist in database return 0
    if(empty($notices)) return 0;

    $noticefound=0;
    $time=time();
    $array=[];

    foreach($notices as $not) {
      $noticeinfo = $this->parse_notice($not['reference']);

      //if encoded string matches, the notice exists
      if($not['reference']===$_GET['umbcnotice']) { $noticefound = $this->parse_notice($not['reference']); $noticefound['message'] = wp_kses_post($not['message']); }

      //if its not the same notice but its not older than 3 minutes keep it in the array
      if($time - $noticeinfo['timestamp'] < 180 && $not['reference']!==$_GET['umbcnotice']) $array[]=$not;

    }

    $notices = $this->encode_serialize($array);
    //update notices option
    update_option('um-bootstrap-carousels-notices', $notices);

    //return verbose notice array
    return $noticefound;
  }

  public function render_notice() {
    $notice = $this->get_notice();
    if(!$notice) return 0;

    $class = $notice['type']==='s' ? 'notice-success' : 'notice-error';

    $html = '<div class="notice is-dismissible '.$class.'">';
    $html .= '<p>'.$notice['message'].'</p>';
    $html .= '<button type="button" class="notice-dismiss">';
    $html .= '<span class="screen-reader-text">Dismiss this notice.</span>';
    $html .= '</button>';
    $html .= '</div>';

    return $html;
  }

  /**
   * Add a new slider
   * Create a new slider with a title and empty array
   *
   */
  public function add_slider() {
    $appendtoredirect = '';
    if(isset($_POST['um_add_slider_nonce']) && wp_verify_nonce($_POST['um_add_slider_nonce'], 'um_add_slider_nonce')) {
      $sliders = $this->get_sliders();

      $title = sanitize_text_field($_POST['umnewslidertitle']);

      if(!count($sliders)) {
        $id = 1;
      } else {
        //sort sliders numerically
        ksort($sliders);
        //set id to largest id and add 1
        end($sliders);
        $id = key($sliders);
        $id = intval($id) + 1;
      }
      $sliders[$id]=array(
        'title' => $title,
        'items' => array(),
        'settings' => array(
          'autobootstrap' => 0,
          'style' => 0
        )
      );
      $sliders = $this->encode_serialize($sliders);
      update_option('um-bootstrap-carousels', $sliders);

      $appendtoredirect.=$this->create_notice(
        __("Carousel Added", "um-bootstrap-carousels"),
        's'
      );
      $appendtoredirect.="&umbc=".$id;

    } else {
      $appendtoredirect.=$this->create_notice(
        __("Bad Nonce", "um-bootstrap-carousels"),
        'e'
      );
    }

    wp_redirect(admin_url( "admin.php?page=carousel-management".$appendtoredirect));
    exit();

  }
  /**
   * Add a new slider item
   *
   *   Add item reference
   *    $id => array(
   *      'title' => 'string'
   *      'attachmentid' => 'string',
   *      'pointerurl' => 'url',
   *      'pointertext' => 'string',
   *      'details' => 'html'
   *    );
   *
   **/
  public function add_slider_item() {
    $appendtoredirect = '';
    if(isset($_POST['um_add_slider_item_nonce']) && wp_verify_nonce($_POST['um_add_slider_item_nonce'], 'um_add_slider_item_nonce')) {
      $sliderid = intval($_POST['umsliderid']);

      $itemtitle = sanitize_text_field($_POST['umaddslideritemtitle']);
      $sliders=$this->get_sliders();
      $attachmentid = isset($_POST['umaddslideritemimage']) ? sanitize_text_field($_POST['umaddslideritemimage']) : '';
      $details = isset($_POST['umaddslideritemdetails']) ? wp_kses_post($_POST['umaddslideritemdetails']) : '';
      $pointerurl = isset($_POST['umaddslideritempointerurl']) ? strip_tags( stripslashes( filter_var($_POST['umaddslideritempointerurl'], FILTER_VALIDATE_URL) ) ) : '';
      $pointertext = isset($_POST['umaddslideritempointer']) ? sanitize_text_field($_POST['umaddslideritempointer']) : '';
      $slideritems = $sliders[$sliderid]['items'];
      //sort slider items
      ksort($sliders[$sliderid]['items']);

      if(!count($slideritems)) {
        $id = 1;
      } else {
        end($slideritems);
        $id = key($slideritems);
        $id = intval($id) + 1;
      }
      $sliders[$sliderid]['items'][$id] = array(
        'title' => $itemtitle,
        'attachmentid' => $attachmentid,
        'pointerurl' => $pointerurl,
        'pointertext' => $pointertext,
        'details' => $details
      );

      $sliders = $this->encode_serialize($sliders);
      update_option('um-bootstrap-carousels', $sliders);

      $appendtoredirect.=$this->create_notice(
        __("Carousel Item Added!", "um-bootstrap-carousels"),
        's'
      );
      $appendtoredirect.="&umbc=".$sliderid;

    } else {
      $appendtoredirect.=$this->create_notice(
        __("Bad Nonce", "um-bootstrap-carousels"),
        'e'
      );
    }

    wp_redirect(admin_url( "admin.php?page=carousel-management".$appendtoredirect));
    exit();
  }

  /**
   * Edit a slider item
   *
   *
   **/
  public function edit_slider_item() {
    $appendtoredirect = '';
    if(isset($_POST['um_edit_slider_item_nonce']) && wp_verify_nonce($_POST['um_edit_slider_item_nonce'], 'um_edit_slider_item_nonce')) {
      $sliderid = intval($_POST['umsliderid']);
      $slideritemid = intval($_POST['umslideritemid']);

      $itemtitle = sanitize_text_field($_POST['umslideredititemtitle']);
      $sliders=$this->get_sliders();
      $attachmentid = (isset($_POST['umslideredititemimage']) && !empty($_POST['umslideredititemimage'])) ? sanitize_text_field($_POST['umslideredititemimage']) : '';
      $details = (isset($_POST['umslideredititemdetails']) && !empty($_POST['umslideredititemdetails'])) ? wp_kses_post($_POST['umslideredititemdetails']) : '';
      $pointerurl = (isset($_POST['umslideredititempointerurl']) && !empty($_POST['umslideredititempointerurl'])) ? strip_tags( stripslashes( filter_var($_POST['umslideredititempointerurl'], FILTER_VALIDATE_URL) ) ) : '';
      $pointertext = (isset($_POST['umslideredititempointer']) && !empty($_POST['umslideredititempointer'])) ? sanitize_text_field($_POST['umslideredititempointer']) : '';


      $sliders[$sliderid]['items'][$slideritemid] = array(
        'title' => $itemtitle,
        'attachmentid' => $attachmentid,
        'pointerurl' => $pointerurl,
        'pointertext' => $pointertext,
        'details' => $details
      );
      //sort slider items
      ksort($sliders[$sliderid]['items']);

      $sliders = $this->encode_serialize($sliders);
      update_option('um-bootstrap-carousels', $sliders);

      $appendtoredirect.=$this->create_notice(
        __("Carousel Item Added!", "um-bootstrap-carousels"),
        's'
      );
      $appendtoredirect.="&umbc=".$sliderid;

    } else {
      $appendtoredirect.=$this->create_notice(
        __("Bad Nonce", "um-bootstrap-carousels"),
        'e'
      );
    }

    wp_redirect(admin_url( "admin.php?page=carousel-management".$appendtoredirect));
    exit();
  }

  public function delete_slider() {
    $appendtoredirect='';
    if(isset($_POST['um_delete_slider_nonce']) && wp_verify_nonce($_POST['um_delete_slider_nonce'], 'um_delete_slider_nonce')) {
      $id = intval($_POST['umsliderid']);

      $sliders = $this->get_sliders();

      $updatedsliders = array();
      foreach($sliders as $sliderid => $sliderarray) {
        if($sliderid!==$id) $updatedsliders[$sliderid] = $sliderarray;
      }
      //sort slider keys numerically
      ksort($updatedsliders);
      $sliders = $this->encode_serialize($updatedsliders);

      update_option('um-bootstrap-carousels', $sliders);

      $appendtoredirect.=$this->create_notice(
        __("Carousel Successfully Deleted", "um-bootstrap-carousels"),
        's'
      );

    } else {
      $appendtoredirect.=$this->create_notice(
        __("Bad Nonce", "um-bootstrap-carousels"),
        'e'
      );
    }

    wp_redirect(admin_url( "admin.php?page=carousel-management".$appendtoredirect));
    exit();



  }

  public function delete_slider_item() {
    $appendtoredirect = '';
    if(isset($_POST['um_delete_slider_item_nonce']) && wp_verify_nonce($_POST['um_delete_slider_item_nonce'], 'um_delete_slider_item_nonce')) {
      $sliderid = intval($_POST['umsliderid']);
      $itemid = intval($_POST['umslideritemid']);

      $sliders = $this->get_sliders();

      //make a new array for slider data
      $updatedsliders = array();

      //cycle through original slider array
      foreach($sliders as $thesliderid => $sliderarray) {
        //collect all data not inside selected slider array
        if($thesliderid!==$sliderid) $updatedsliders[$thesliderid] = $sliderarray;
        //rebuild the current slider array
        if($thesliderid===$sliderid) {
          //set the title of the slider
          $updatedsliders[$sliderid]['title'] = $sliders[$sliderid]['title'];
          //recreate the items array
          $updatedsliders[$sliderid]['items'] = array();
          foreach($sliderarray['items'] as $slideritemid => $slideritemarray) {
            //rebuild the items array without the item to delete
            if($slideritemid!==$itemid) {
              $updatedsliders[$sliderid]['items'][$slideritemid] = $slideritemarray;
            }
          }
          //sort item keys numerically
          ksort($updatedsliders[$sliderid]['items']);
        }

      }
      //sort slider keys numerically
      ksort($updatedsliders);

      $sliders = $this->encode_serialize($updatedsliders);

      update_option('um-bootstrap-carousels', $sliders);

      $appendtoredirect.=$this->create_notice(
        __("Carousel Item Successfully Deleted", "um-bootstrap-carousels"),
        's'
      );

      $appendtoredirect.="&umbc=".$sliderid;
    } else {
      $appendtoredirect.=$this->create_notice(
        __("Bad Nonce", "um-bootstrap-carousels"),
        'e'
      );
    }

    wp_redirect(admin_url( "admin.php?page=carousel-management".$appendtoredirect));
    exit();



  }

  /**
   * Settings array reference
   *
   * array(
   *  'bootstrap' => array (
   *    'version' => 'string'
   *  ),
   *  'styles' => array()
   * );
   *
   */

  private function get_settings() {
    $settings = get_option('um-bootstrap-carousels-settings');
    if(!$settings) return false;

    $settings = $this->unserialize_decode($settings);

    return $settings;
  }

  public function save_slider_settings() {
    $appendtoredirect='';
    if(isset($_POST['umbc_save_settings_nonce']) && wp_verify_nonce($_POST['umbc_save_settings_nonce'], 'umbc_save_settings_nonce')) {
      $settings = $this->get_settings();
      $sliders = $this->get_sliders();

      $sliderid = intval($_POST['umbc_slider_id']);

      //only use value from POST if it is int AND 1 or 0
      $auto = isset($_POST['umbc_settings_auto_bootstrap']) ? intval($_POST['umbc_settings_auto_bootstrap']) : 0;
      $auto = ($auto!==1 && $auto!==0) ? 0 : $auto;

      //only use value that matches POST if it is int AND a matching value exists in the style array
      $style = intval($_POST['umbc_settings_style']);
      $updatestyle = 0;
      foreach($settings['styles'] as $key => $styleoption) {
        if($style===$key) $updatestyle=$key;
      }
      $sliders[$sliderid]['settings'] = array(
        'autobootstrap' => $auto,
        'style' => $updatestyle
      );

      $sliders = $this->encode_serialize($sliders);
      update_option('um-bootstrap-carousels', $sliders);

      $appendtoredirect.=$this->create_notice(
        __("Settings Saved", "um-bootstrap-carousels"),
        's'
      );
      $appendtoredirect="&umbc=".$sliderid;
    } else {
     $appendtoredirect.=$this->create_notice(
       __("Bad Nonce", "um-bootstrap-carousels"),
       'e'
     );
    }
    wp_redirect(admin_url( "admin.php?page=carousel-management".$appendtoredirect));
    exit();

  }

  /**
   * Usage of GET for slider id
   *
   *
   *
   **/
  public function display_menu_page() {
    $sliders = $this->get_sliders();
    $settings = $this->get_settings();
    $selectedslider = false;
    if(isset($_GET['umbc'])){
      foreach($sliders as $sliderid => $sliderarray) {
          if($sliderid===intval($_GET['umbc'])) $selectedslider = $sliderid;
      }
    }

    $umbcnotice = $this->render_notice();

    include_once dirname( __FILE__ ).'/display/um-bootstrap-carousel-admin-display.php';
  }

  /**
   *
   * encode svg for menu icon
   *
   **/
  public function encode_svg() {
    $svg ='<?xml version="1.0" encoding="utf-8"?>
      <!-- Generator: Adobe Illustrator 23.0.2, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
      <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
      	 viewBox="0 0 1144 1080" style="enable-background:new 0 0 1144 1080;" xml:space="preserve">
      <style type="text/css">
      	.st0{stroke:#000000;stroke-miterlimit:10;}
      </style>
      <path class="st0" d="M593.95,498.05c-1.03,3,33.16,29.27,54.42,16.47c18.54-11.16,15.66-45.06,5.5-64.84
      	c-16.82-32.74-64.71-48.47-100.88-34.23c-43.64,17.18-56.34,72.53-50.12,109.85c9.52,57.17,67.85,98.58,123.96,97.85
      	c68.9-0.89,114.13-65.03,126.78-115.18c20.38-80.79-33.46-166.32-104.72-198.2c-88.28-39.5-182.72,12.53-229.15,61.59
      	c-88.53,93.54-79.81,257.95,1.1,362.79c75.95,98.41,206.69,132.93,315.15,105.46c156.76-39.71,232.49-200.19,240.67-322.76
      	c11.82-177.07-112.47-347.68-287.37-399.03c-186.61-54.78-370.79,43.82-460.54,184.1c-135.01,211.01-9.29,442.71-1.14,457.13
      	c94.41,167.1,277.35,225.42,282.53,217.89c5.02-7.31-158.87-73.99-237.19-242.92c-12.51-26.97-103.79-223.85,6.89-391.09
      	c81.69-123.44,260.25-210.87,419.81-142.22c131.87,56.73,217.71,205.85,192.96,344.15c-3.23,18.04-33.63,173.29-168.85,209.54
      	c-91.87,24.63-187.91-17.51-241.04-86.88c-51.39-67.1-77.9-179.63-19.84-253.85c30.37-38.82,94.99-81.37,162.01-58.8
      	c53.96,18.17,96.77,74.28,91.63,134.23c-4,46.66-38.1,108.15-91.21,108.31c-50.43,0.15-97.11-55.06-89.61-99.88
      	c4.08-24.39,25.54-53.72,52.71-54.11c18.1-0.26,36.75,12.35,43.85,28.86c1.06,2.47,7.43,17.26,0.89,24.98
      	C623.64,508.47,594.85,495.41,593.95,498.05z"/>
      </svg>';
    $svg = trim($svg);
    return base64_encode($svg);
  }

  public function admin_plugin_global_actions() {

    add_action('admin_post_um_add_slider', array( $this, 'add_slider') );

    add_action('admin_post_um_delete_slider', array( $this, 'delete_slider') );

    add_action('admin_post_um_add_slider_item', array( $this, 'add_slider_item') );

    add_action('admin_post_um_edit_slider_item', array( $this, 'edit_slider_item') );

    add_action('admin_post_um_delete_slider_item', array( $this, 'delete_slider_item') );

    add_action('admin_post_umbc_save_slider_settings', array( $this, 'save_slider_settings') );

    add_action( 'admin_init', array( $this, 'create_query_vars' ) );

  }

  public function add_to_admin() {
    $pagetitle = __("Carousel Management", "um-bootstrap-carousels");
    $menutitle = __("Carousels", "um-bootstrap-carousels");
    $iconsvg='data:image/svg+xml;base64,'.$this->encode_svg();
    $hook = add_menu_page($pagetitle, $menutitle, 'manage_options', 'carousel-management', array($this, 'display_menu_page'), $iconsvg);
    //only enqueue css js and assets for plugin pages
    add_action('load-' . $hook, array($this, 'enqueue') );
  }

}
