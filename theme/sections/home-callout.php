<?php $yoga_callout_enable = get_theme_mod('yoga_callout_enable','true');
if($yoga_callout_enable !='false')
{
$yoga_callout_background = get_theme_mod('yoga_callout_background');
$yoga_callout_overlay_color = get_theme_mod('yoga_callout_overlay_color');
$yoga_callout_text_align = get_theme_mod('yoga_callout_text_align');
$yoga_callout_button_one_label = get_theme_mod('yoga_callout_button_one_label');
$yoga_callout_button_one_link = get_theme_mod('yoga_callout_button_one_link');
$yoga_callout_button_one_target = get_theme_mod('yoga_callout_button_one_target');
$yoga_callout_button_two_label = get_theme_mod('yoga_callout_button_two_label');
$yoga_callout_button_two_link = get_theme_mod('yoga_callout_button_two_link');
$yoga_callout_button_two_target = get_theme_mod('yoga_callout_button_two_target'); ?>
<!--==================== CALLOUT SECTION ====================-->
<?php if($yoga_callout_background != '') { ?>

<section class="yoga-callout" style="background-image:url('<?php echo esc_url($yoga_callout_background);?>');">
<?php } else { ?>
<section class="yoga-callout">
  <?php } ?>
  <div class="overlay" style="background-color:<?php echo esc_attr($yoga_callout_overlay_color);?>;">
    <div class="container">
      <div class="row">
        <div class="yoga-callout-inner text-xs text-<?php echo $yoga_callout_text_align; ?>">
          <?php $yoga_callout_title = get_theme_mod('yoga_callout_title');
          
            if( !empty($yoga_callout_title) ):

              echo '<h3 class="padding-bottom-30">'.esc_attr($yoga_callout_title).'</h3>';

            endif; ?>
          <?php $yoga_callout_description = get_theme_mod('yoga_callout_description');

            if( !empty($yoga_callout_description) ):

              echo '<p>'.esc_attr($yoga_callout_description).'</p>';

            endif; ?>
          <div class="padding-top-20">
          <?php if( !empty($yoga_callout_button_one_label) ): ?>
      		  <a href="<?php echo esc_url($yoga_callout_button_one_link); ?>" <?php if( $yoga_callout_button_one_target == true) { echo "target='_blank'"; } ?> class="btn btn-theme-two margin-bottom-10">
      		<?php echo esc_attr($yoga_callout_button_one_label); ?></a>
      		<?php
      		endif;

          if( !empty($yoga_callout_button_two_label) ): ?>
      		  <a href="<?php echo esc_attr($yoga_callout_button_two_link); ?>" <?php if( $yoga_callout_button_two_target ==true) { echo "target='_blank'"; } ?> class="btn btn-theme margin-bottom-10"><?php echo esc_attr($yoga_callout_button_two_label); ?></a>
    		<?php endif; ?>	
        </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php } ?>