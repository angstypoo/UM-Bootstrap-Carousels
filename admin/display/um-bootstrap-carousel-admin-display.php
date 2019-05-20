<?php

/**
 * Umethod Slider Manager - Carousel Managment Tool
 * Admin Facing Markup
 *
 *
 *
 * @package um-bootstrap-carousels
 */

?>

<div id="umslidermanager" class="wrap">

  <div class="row umslidertitle">
    <div class="col-auto">
      <h1 class="wp-heading-inline"><?php _e('Manage Carousels', 'um-bootstrap-carousels'); ?></h1>
      <?php if($umbcnotice) { echo $umbcnotice; } ?>
    </div>
  </div>

  <div class="row umslideroptions">
    <div class="col-auto">

      <div class="btn-group ml-2">
        <button type="button" class="btn btn-outline-secondary" data-toggle="collapse" data-target="#umslideraddform"><?php _e('Add Slider/Carousel', 'um-bootstrap-carousels'); ?></button>
      </div>

      <?php if(count($sliders)) : ?>
      <div class="btn-group ml-2">
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php _e('Carousels', 'um-bootstrap-carousels'); ?></button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <?php foreach($sliders as $sliderid => $sliderarray) : ?>
              <a class="dropdown-item" href="<?php echo esc_url( admin_url('admin.php?page=carousel-management') ) . '&umbc='.$sliderid;?>"><?php echo esc_html($sliderarray['title']); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php
      //create nonce for add slider
      $addslidernonce = wp_create_nonce('um_add_slider_nonce');

      ?>
      <div class="collapse" id="umslideraddform">
        <div class="card card-body">
          <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) );?>" method="post">
            <input type="hidden" name="um_add_slider_nonce" value="<?php echo $addslidernonce; ?>">
            <input type="hidden" name="action" value="um_add_slider">
            <div class="form-group">
              <label for="umnewslidertitle"><?php _e('Carousel Title', 'um-bootstrap-carousels'); ?></label>
              <input type="text" class="form-control" id="umnewslidertitle" name="umnewslidertitle" placeholder="<?php _e('Enter title', 'um-bootstrap-carousels'); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><?php _e('Add Carousel', 'um-bootstrap-carousels'); ?></button>
          </form>
        </div>
      </div>

    </div>

  </div>
  <?php if($selectedslider) : ?>

    <?php $slideritems = $sliders[$selectedslider]['items']; ?>

    <div id="umselectedslider">
      <div class="row">
        <div class="col-auto">
          <h2 class="title mb-3 mt-5"><?php _e('Manage Carousel: ', 'um-bootstrap-carousels'); ?> <?php echo esc_html($sliders[$selectedslider]['title']); ?></h2>
          <h1 class="wp-heading-inline"><?php _e('Usage:', 'um-bootstrap-carousels'); ?></h1>
          <p><?php echo sprintf(__('Use this shortcode in a page: [umcarousel id="%s"]', 'um-bootstrap-carousels'), $selectedslider); ?></p>
          <p><?php echo sprintf(__('You may optionally add a custom wrapper css id and class like this: [umcarousel id="%s" cssid="mycustomcssid" cssclass="mycustomcssclass"]', 'um-bootstrap-carousels'), $selectedslider); ?></p>
        </div>
      </div>
      <!--Selected Slider options -->
      <div class="row">
        <div class="col-auto">
          <div class="btn-group ml-2">
            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#umslideradditem" aria-expanded="false" aria-controls="collapseExample"><?php _e('Add Item', 'um-bootstrap-carousels'); ?></button>
          </div>
          <div class="btn-group ml-2">
            <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#umbcsettings"><?php _e('Settings', 'um-bootstrap-carousels'); ?></button>
          </div>
          <div class="btn-group ml-2">
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#umsliderconfirmdelete"><?php _e('Delete', 'um-bootstrap-carousels'); ?> <?php echo esc_html($sliders[$selectedslider]['title']);?></button>
          </div>
        </div>
      </div>
      <!--confirm delete -->
      <div class="modal fade" id="umsliderconfirmdelete" tabindex="-1" role="dialog" aria-labelledby="umsliderconfirmdeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="umsliderconfirmdeleteLabel"><?php _e('Delete', 'um-bootstrap-carousels'); ?> <?php echo esc_html($sliders[$selectedslider]['title']); ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <?php echo sprintf(__('Are you sure you want to delete Carousel: %s?', 'um-bootstrap-carousels'), esc_html($sliders[$selectedslider]['title'])); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Cancel', 'um-bootstrap-carousels'); ?></button>
              <?php
              //create nonce delete slider
              $deleteslidernonce = wp_create_nonce('um_delete_slider_nonce');
              ?>
              <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) );?>" method="post">
                <input type="hidden" name="um_delete_slider_nonce" value="<?php echo $deleteslidernonce; ?>">
                <input type="hidden" name="action" value="um_delete_slider">
                <input type="hidden" name="umsliderid" value="<?php echo $selectedslider; ?>">
                <button type="submit" class="btn btn-danger"><?php _e('Delete', 'um-bootstrap-carousels'); ?></button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!--Add Item -->
      <div class="modal fade" id="umslideradditem" tabindex="-1" role="dialog" aria-labelledby="umslideradditemLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="umslideradditemLabel"><?php _e('Add new item', 'um-bootstrap-carousels'); ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
            //create nonce add slider item
            $addslideritemnonce = wp_create_nonce('um_add_slider_item_nonce');
            ?>
            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) );?>" method="post">
              <div class="modal-body">
                <input type="hidden" name="um_add_slider_item_nonce" value="<?php echo $addslideritemnonce; ?>">
                <input type="hidden" name="action" value="um_add_slider_item">
                <input type="hidden" name="umsliderid" value="<?php echo $selectedslider; ?>">
                <label for="umaddslideritemtitle"><?php _e('Item Title', 'um-bootstrap-carousels'); ?></label>
                <input type="text" class="form-control" id="umaddslideritemtitle" name="umaddslideritemtitle" placeholder="<?php _e('Enter title', 'um-bootstrap-carousels'); ?>" required>
                <input type="hidden" id="umaddslideritemimage" name="umaddslideritemimage">
                <div class="umaddslideritemimagepreview">
                  <img id="umaddslideritemimagepreview" src="<?php echo plugin_dir_url( __FILE__ ).'images/no-image.png'?>">
                </div>
                <div class="btn-group mt-2 mb-2 text-center col">
                  <button id="umaddslideritemimageupload" type="button" class="btn btn-outline-secondary"><?php _e('Upload Image', 'um-bootstrap-carousels'); ?></button>
                </div>
                <label for="umaddslideritemdetails"><?php _e('Details', 'um-bootstrap-carousels'); ?></label>
                <?php
                  $wp_editor_settings = array (
                    'media_buttons' => false,
                    'textarea_rows' => 4
                  );
                  wp_editor('', 'umaddslideritemdetails', $wp_editor_settings);
                ?>
                <label for="umaddslideritempointer"><?php _e('Link Text', 'um-bootstrap-carousels'); ?></label>
                <input type="text" class="form-control" id="umaddslideritempointer" name="umaddslideritempointer" placeholder="<?php _e('Enter Link text', 'um-bootstrap-carousels'); ?>">
                <label for="umaddslideritempointerurl"><?php _e('Link URL', 'um-bootstrap-carousels'); ?></label>
                <input type="text" class="form-control" id="umaddslideritempointerurl" name="umaddslideritempointerurl" placeholder="<?php _e('Enter URL', 'um-bootstrap-carousels'); ?>">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Cancel', 'um-bootstrap-carousels'); ?></button>
                <button type="submit" class="btn btn-danger"><?php _e('Add Item', 'um-bootstrap-carousels'); ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!--Selected Slider Settings -->
      <div class="modal fade" id="umbcsettings" tabindex="-1" role="dialog" aria-labelledby="umbcsettingslabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="umbcsettingslabel"><?php echo ucfirst( esc_html($sliders[$selectedslider]['title']) ).' '.__('Settings', 'um-bootstrap-carousels'); ?></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php
            //create nonce add slider item
            $settingsnonce = wp_create_nonce('umbc_save_settings_nonce');
            if(!isset($sliders[$selectedslider]['settings'])) {
              $sliders[$selectedslider]['settings']=array(
                'autobootstrap' => 0,
                'style' => 0
              );
            }
            ?>
            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) );?>" method="post">
              <div class="modal-body">
                <input type="hidden" name="umbc_save_settings_nonce" value="<?php echo $settingsnonce; ?>">
                <input type="hidden" name="action" value="umbc_save_slider_settings">
                <input type="hidden" name="umbc_slider_id" value="<?php echo $selectedslider; ?>">
                <p><?php _e("If you would like to automatically include bootstrap when this slider's shortcode is included, toggle this on.  By default it is not included and you need to manually include bootstrap.", "um-bootstrap-carousels"); ?></p>
                <div class="custom-control custom-switch">
                  <input type="checkbox" name="umbc_settings_auto_bootstrap" class="custom-control-input" id="umbcautobootstrap" value="1" <?php if($sliders[$selectedslider]['settings']['autobootstrap']) echo "checked"; ?>>
                  <label class="custom-control-label" for="umbcautobootstrap"><?php echo sprintf(__('Automatically include bootstrap version: %s', 'um-bootstrap-carousels'),$settings['bootstrap']['version']); ?></label>
                </div>
                <div class="mt-5">
                  <p><?php _e('Optionally select a style for the slider output code. By default there are is no added styling on top of bootstrap','um-bootstrap-carousels');?></p>
                  <button class="btn btn-secondary dropdown-toggle" type="button" id="settingsdropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php _e('Style', 'um-bootstrap-carousels'); ?></button>
                  <div id="umbcsettingsstyledd" class="dropdown-menu" aria-labelledby="settingsdropdownMenuButton">
                    <?php foreach($settings['styles'] as $stylekey => $stylename) : ?>
                      <a id="<?php echo $stylekey?>" class="dropdown-item<?php if($stylekey===$sliders[$selectedslider]['settings']['style']) echo " active"; ?>" href="#"><?php echo $stylename; ?></a>
                    <?php endforeach; ?>
                  </div>
                  <input type="hidden" name="umbc_settings_style" id="umbc_settings_style" value="<?php if(isset($sliders[$selectedslider]['settings']['style'])) echo $sliders[$selectedslider]['settings']['style']; ?>">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Cancel', 'um-bootstrap-carousels'); ?></button>
                <button type="submit" class="btn btn-danger"><?php _e('Save Settings', 'um-bootstrap-carousels'); ?></button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <!--Selected Slider Items-->
      <?php if(count($slideritems)) : ?>

        <?php
          $deleteslideritemnonce = wp_create_nonce('um_delete_slider_item_nonce');
          $editslideritemnonce = wp_create_nonce('um_edit_slider_item_nonce');
        ?>

        <div class="row umslideritems justify-content-start">
          <?php foreach($slideritems as $slideritemid => $slideritem) : ?>
            <div class="col-md">
              <h4><?php echo esc_html($slideritem['title']); ?></h4>
              <div class="umslideritemimagepreview">
                <img class="umslideritemimage" src="<?php if(!isset($slideritem['attachmentid']) || !wp_get_attachment_image_url(intval($slideritem['attachmentid']))) { echo plugin_dir_url( __FILE__ ).'images/no-image.png'; } else { echo wp_get_attachment_image_url(intval($slideritem['attachmentid']), 'medium');}?>">
              </div>
              <div class="mt-2">
                <button id="umslideritemeditbutton<?php echo intval($slideritemid); ?>" type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#umslideredititem"><?php _e('Edit', 'um-bootstrap-carousels'); ?></button>
                <button type="button" class="btn btn-outline-danger btn-sm"  data-toggle="modal" data-target="#umslideritemconfirmdelete<?php echo intval($slideritemid); ?>"><?php _e('Delete', 'um-bootstrap-carousels'); ?></button>
              </div>
            </div>
            <!--confirm delete -->
            <div class="modal fade" id="umslideritemconfirmdelete<?php echo intval($slideritemid); ?>" tabindex="-1" role="dialog" aria-labelledby="umslideritemconfirmdelete<?php echo intval($slideritemid); ?>Label" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="umslideritemconfirmdelete<?php echo intval($slideritemid); ?>Label"><?php _e('Delete', 'um-bootstrap-carousels'); ?> <?php echo esc_html($slideritem['title']); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <?php echo sprintf(__('Are you sure you want to delete Carousel Item: %s?', 'um-bootstrap-carousels'), esc_html($slideritem['title'])); ?>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Cancel', 'um-bootstrap-carousels'); ?></button>
                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) );?>" method="post">
                      <input type="hidden" name="um_delete_slider_item_nonce" value="<?php echo $deleteslideritemnonce; ?>">
                      <input type="hidden" name="action" value="um_delete_slider_item">
                      <input type="hidden" name="umsliderid" value="<?php echo $selectedslider; ?>">
                      <input type="hidden" name="umslideritemid" value="<?php echo intval($slideritemid); ?>">
                      <button type="submit" class="btn btn-danger"><?php _e('Delete', 'um-bootstrap-carousels'); ?></button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- edit item -->
        <div class="modal fade" id="umslideredititem" tabindex="-1" role="dialog" aria-labelledby="umslideredititemLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="umslideredititemLabel"><?php _e('Edit Item', 'um-bootstrap-carousels'); ?></h5>
                <button id="umeditsliderclose" type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) );?>" method="post">
                <div class="modal-body">
                  <input type="hidden" name="um_edit_slider_item_nonce" value="<?php echo $editslideritemnonce ; ?>">
                  <input type="hidden" name="action" value="um_edit_slider_item">
                  <input type="hidden" name="umsliderid" value="<?php echo $selectedslider; ?>">
                  <input type="hidden" id="umslideritemid" name="umslideritemid">
                  <label for="umslideredititemtitle"><?php _e('Item Title', 'um-bootstrap-carousels'); ?></label>
                  <input type="text" class="form-control" id="umslideredititemtitle" name="umslideredititemtitle" placeholder="<?php _e('Item Title', 'um-bootstrap-carousels'); ?>" value="" required>
                  <input type="hidden" id="umslideredititemimage" name="umslideredititemimage">
                  <div class="umslideredititemimagepreview">
                    <img id="umslideredititemimagepreview" src="<?php echo plugin_dir_url( __FILE__ ).'images/no-image.png'?>">
                  </div>
                  <div class="btn-group mt-2 mb-2 text-center col">
                    <button id="umslideredititemimageupload" type="button" class="btn btn-outline-secondary"><?php _e('Change Image', 'um-bootstrap-carousels'); ?></button>
                  </div>
                  <label for="umslideredititemdetails"><?php _e('Details', 'um-bootstrap-carousels'); ?></label>
                  <?php
                    $wp_editor_settings = array (
                      'media_buttons' => false,
                      'textarea_rows' => 4,
                      'remove_linebreaks' => false
                    );
                    wp_editor('', 'umslideredititemdetails', $wp_editor_settings);
                  ?>
                  <label for="umslideredititempointer"><?php _e('Link Text', 'um-bootstrap-carousels'); ?></label>
                  <input type="text" class="form-control" id="umslideredititempointer" name="umslideredititempointer" placeholder="<?php _e('Enter Link text', 'um-bootstrap-carousels'); ?>">
                  <label for="umslideredititempointerurl"><?php _e('Link URL', 'um-bootstrap-carousels'); ?></label>
                  <input type="text" class="form-control" id="umslideredititempointerurl" name="umslideredititempointerurl" placeholder="<?php _e('Enter URL', 'um-bootstrap-carousels'); ?>">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Cancel', 'um-bootstrap-carousels'); ?></button>
                  <button type="submit" class="btn btn-danger"><?php _e('Save Item', 'um-bootstrap-carousels'); ?></button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <?php
          //output slideritem data to json encoded array

          //escape first
          $sanitizeditems = array();
          foreach($slideritems as $theitemkey => $theitem) {
            $selecteditemimgurl = (!isset($theitem['attachmentid']) || !wp_get_attachment_image_url(intval($theitem['attachmentid']))) ? plugin_dir_url( __FILE__ ).'images/no-image.png' : wp_get_attachment_image_url(intval($theitem['attachmentid']), 'medium');
            $selecteditemdetails = wp_specialchars_decode(esc_textarea($theitem['details']));
            $sanitizeditems[intval($theitemkey)]=array(
              'title' => esc_html($theitem['title']),
              'imgurl' => $selecteditemimgurl,
              'attachmentid' => intval($theitem['attachmentid']),
              'pointerurl' => esc_url($theitem['pointerurl']),
              'pointertext' => esc_html($theitem['pointertext']),
              'details' => $selecteditemdetails
            );
          }


          echo '<script>';
            echo 'var umslideritems = '.json_encode($sanitizeditems);
          echo '</script>';
        ?>
      <?php endif; ?>

    </div>

  <?php else: ?>

    <div class="card">
      <h5 class="card-header"><?php _e('Getting Started', 'um-bootstrap-carousels'); ?></h5>
      <div class="card-body">
        <h5 class="card-title"><?php _e('Add a slider or select an existing one', 'um-bootstrap-carousels'); ?></h5>
        <p class="card-text"><?php _e('Click <strong>Add Slider/Carousel</strong> to create a new slider reference.', 'um-bootstrap-carousels'); ?></p>
        <p class="card-text"><?php _e('Click the <strong>Carousels</strong> dropdown to manage the contents of an existing slider.', 'um-bootstrap-carousels'); ?></p>
        <p class="card-text"><?php _e('Slider Usage: [umcarousel id="1" cssid="mycustomcssid" cssclass="mycustomcssclass"]', 'um-bootstrap-carousels'); ?></p>
      </div>
    </div>

  <?php endif; ?>

  <?php
    //if notice get var is set but it is expired, create a js reference for cleanup
    $clearnotice=0;
    if(isset($_GET['umbcnotice']) && !$umbcnotice) $clearnotice=1;
    echo '<script>';
      echo 'var umbcclearnotice = "'.$clearnotice.'";';
    echo '</script>';
  ?>

</div>
