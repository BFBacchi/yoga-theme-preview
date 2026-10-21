<?php
/**
 * The template for displaying search results pages.
 *
 * @package yoga
 */

get_header(); ?>
<!--==================== ti breadcrumb section ====================-->
<?php get_template_part('index','banner'); ?>
<!--==================== main content section ====================-->
<main id="content">
  <div class="container">
    <div class="row">
      <div class="<?php echo ( !is_active_sidebar( 'sidebar_primary' ) ? '12' :'9' ); ?> col-md-9">
        <div class="row">
          <?php 
		global $i;
		if ( have_posts() ) : ?>
		<!-- Search result whole theme -->
		<h2><?php printf( __( "Search Results for: %s", 'yoga' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
		<?php while ( have_posts() ) : the_post();  
		 get_template_part('content','');
		 endwhile; else : ?>
		<h2><?php esc_attr_e( "Nothing found", 'yoga' ); ?></h2>
		<div class="">
		<p><?php esc_attr_e( "Sorry, but nothing matched your search criteria. Please try again with some different keywords.", 'yoga' ); ?>
		</p>
		<?php get_search_form(); ?>
		</div><!-- .blog_con_mn -->
		<?php endif; ?>
         </div>
      </div>
	  <aside class="col-md-3">
        <?php get_sidebar(); ?>
      </aside>
    </div>
  </div>
</main>
<?php
get_footer();
?>