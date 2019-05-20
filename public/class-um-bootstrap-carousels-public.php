<?php

/**
 * um-bootstrap-carousels
 *
 * public plugin class
 *
 * includes shortcode callback for carousel rendering
 *
 * @package um-bootstrap-carousels
 */

class Um_Bootstrap_Carousels_Public {

  private $version;

  public function __construct( $version ) {
    $this->version = $version;
  }

  public function register_public_scripts() {

    wp_register_script('um-carousel-public-js-basic', plugin_dir_url( __FILE__ ).'display/scripts/script-basic.js', array('jquery', 'umis'), $this->version, true);
    wp_register_style('um-carousel-public-css-basic', plugin_dir_url( __FILE__ ).'display/style-basic.css', array(), $this->version);

    //include use of google font for styling in "basic" style for carousels
    wp_register_style('raleway-font', 'https://fonts.googleapis.com/css?family=Raleway:400,500,600', array(), $this->version, false);
  }

  /*
   * add the shortcode
   *
   * ref:
   * takes atts: 'id', 'ccsid', 'cssclass' ('id' is mandatory)
   *
   * examples:
   * [umcarousel id="1"]
   * [umcarousel id="1" cssid="myslider" cssclass="myslider"]
   *
   */
  public function add_shortcode() {
    add_shortcode('umcarousel', array($this, 'render_slider'));
  }

  /**
   *
   * Get the slider/carousel array
   *
   **/
  private function get_sliders() {

    $sliders = get_option('um-bootstrap-carousels');
    if(!$sliders) return $sliders;

    $sliders = unserialize(base64_decode($sliders));

    return $sliders;

  }

  /**
   * Call from shortcode
   * pass shortcode atts
   *
   *
   */
  public function render_slider( $atts, $content = null, $tag = '' ) {
    $atts = shortcode_atts(
      array(
        'id' => false,
        'cssid' => false,
        'cssclass' => false
    ), $atts, $tag );

    if(!$atts['id']) return __('You must specify the slider id.', 'um-bootstrap-carousels');

    $atts['id'] = intval($atts['id']);

    $sliders = $this->get_sliders();

    if(!array_key_exists( $atts['id'], $sliders )) return __("This slider doesn't exist.  Double check the id number in the editor.", 'um-bootstrap-carousels');

    $id = $atts['id'];
    $slider = $sliders[$id];

    $cssid = !$atts['cssid'] ? 'um-slider-'.esc_html($slider['title']) : esc_html($atts['cssid']);
    $cssclass = !$atts['cssclass'] ? 'um-slider-'.esc_html($slider['title']) : esc_html($atts['cssclass']);

    if(intval($slider['settings']['autobootstrap'])===1) {
      if(!wp_script_is('um-slider-bootstrap')) wp_enqueue_script('um-slider-bootstrap');
      if(!wp_style_is('um-slider-bootstrap')) wp_enqueue_style('um-slider-bootstrap');
    }

    if(intval($slider['settings']['style'])===1) {
      if(!wp_script_is('um-carousel-public-js-basic')) wp_enqueue_script('um-carousel-public-js-basic');
      if(!wp_style_is('um-carousel-public-css-basic')) wp_enqueue_style('um-carousel-public-css-basic');
      if(!wp_style_is('raleway-font')) wp_enqueue_style('raleway-font');
      $cssclass.=' um-slider-basic';
    }

    //begin compiling output
    $output = '<div id="'.$cssid.'" class="'.$cssclass.'">';
    $output .= '<div id="'.$cssid.'Captions" class="carousel slide" data-ride="carousel">';

    //carousel indicators
    $output .= '<ol class="carousel-indicators">';
    $outputcounter=0;
    foreach($slider['items'] as $item) :
      $outputclass = $outputcounter===0 ? 'class="active"' : '';
      $output .= '<li data-target="#'.$cssid.'Captions" data-slide-to="'.$outputcounter.'" '.$outputclass.'></li>';
      $outputcounter++;
    endforeach;
    $output.= '</ol>';

    //carousel items
    $output.= '<div class="carousel-inner">';
    $outputcounter=0;
    foreach($slider['items'] as $item) :
      $outputclass = $outputcounter===0 ? ' active' : '';
      $output.= '<div class="carousel-item'.$outputclass.'">';
        $output.= '<div class="um-carousel-image-container d-block w-100">';
          $output.= '<img src="'.wp_get_attachment_url(intval($item['attachmentid'])).'" class="d-block carouselimg" alt="">';
        $output.= '</div>';
        $output.= '<div class="carousel-caption d-md-block">';
          $output.= '<div class="um-caption-h">';
            $output.= '<h5>'.esc_html($item['title']).'</h5>';
          $output.= '</div>';
          $output.= '<div class="um-caption-p">';
            $details = wp_specialchars_decode(esc_textarea($item['details']));
            $details = explode("\n", $details);
            foreach($details as $detailp) {
              $output.= '<p>'.$detailp.'</p>';
            }
          $output.= '</div>';
          $output.= '<div class="um-caption-a">';
            $output.= '<a href="'.esc_url($item['pointerurl']).'">'.esc_html($item['pointertext']).'</a>';
          $output.= '</div>';
        $output.= '</div>';
      $output.= '</div>';
      $outputcounter++;
    endforeach;
    $output.= '</div>';

    //carousel controls
    $output.= '<a class="carousel-control-prev" href="#'.$cssid.'Captions" role="button" data-slide="prev">';
    $output.= '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
    $output.= '<span class="sr-only">Previous</span>';
    $output.= '</a>';
    $output.= '<a class="carousel-control-next" href="#'.$cssid.'Captions" role="button" data-slide="next">';
    $output.= '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
    $output.= '<span class="sr-only">Next</span>';
    $output.= '</a>';

    //close main divs
    $output.= '</div>';
    $output.= '</div>';

    return $output;

  }

}
