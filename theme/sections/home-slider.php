<?php 
$yoga_slider_enable = get_theme_mod('yoga_slider_enable','true');
if($yoga_slider_enable != 'false')
{ ?>
<!--==================== SLIDER SECTION ====================-->
<section class="yoga-slider-warraper">
	<div id="yoga-slider" class="owl-carousel">
    	<?php if(is_active_sidebar( 'sidebar-slider' )):
        	dynamic_sidebar( 'sidebar-slider' );
    	endif; ?>
	</div>
</section>
<?php } ?>
<div class="clearfix"></div>