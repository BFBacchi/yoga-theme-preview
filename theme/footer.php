<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package yoga
 */
?>
<!--==================== FOOTER====================-->
<?php 
  $yoga_footer_widget_background = get_theme_mod('yoga_footer_widget_background');
   if($yoga_footer_widget_background != '') { ?>
<footer style="background-image:url('<?php echo esc_url($yoga_footer_widget_background);?>');">
  <?php } else { ?>
<footer> 
  <?php } ?>
  <div class="overlay">
  <!--Start yoga-footer-widget-area-->
  <?php if ( is_active_sidebar( 'footer_widget_area' ) ) { ?>
  <div class="yoga-footer-widget-area">
    <div class="container">
      <div class="row">
        <?php  dynamic_sidebar( 'footer_widget_area' ); ?>
      </div>
    </div>
  </div>
  <?php } ?>
  <!--End yoga-footer-widget-area-->
  <div class="yoga-footer-copyright">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
			<!-- copyright name and sites -->
			<p>&copy; <?php echo esc_html(date('Y')).' '; bloginfo( 'name' ); ?> | <?php printf( esc_html__( 'Theme by %1$s', 'yoga' ),  '<a href="'.esc_url('https://www.themeansar.com').'" rel="designer">Themeansar</a>' ); ?></p>
		</div>
        <div class="col-md-6 col-sm-6 text-right text-center-xs">
          <?php if ( has_nav_menu( 'social' ) ) : ?>
          <nav class="yoga-social-navigation" role="navigation">
            <?php
              wp_nav_menu( array(
                'theme_location' => 'social',
                'menu_class'     => 'social-links-menu',
                'depth'          => 1,
                'link_before'    => '<span class="screen-reader-text">',
                'link_after'     => '</span>' . yoga_include_svg_icons( array( 'icon' => 'chain' ) ),
              ) );
            ?>
          </nav><!-- .social-navigation -->
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  </div>
</footer>
</div>
<!--Scroll To Top--> 
<a href="#" class="ta_upscr bounceInup animated"><i class="fa fa-caret-up"></i></a> 
<!--/Scroll To Top-->
<?php wp_footer(); ?>
</body>
</html>