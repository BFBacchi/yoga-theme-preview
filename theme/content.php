<?php
/**
 * The template for displaying the content.
 * @package yoga
 */
?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="yoga-blog-post-box"> 
		<?php if(has_post_thumbnail()): ?>
	  		<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" class="yoga-blog-thumb">
	  			<?php $defalt_arg =array('class' => "img-responsive"); ?>
	  			<?php the_post_thumbnail('', $defalt_arg); ?>
	  			<span class="yoga-blog-date"><?php echo get_the_date('M'); ?> <?php echo get_the_date('j,'); ?> <?php echo get_the_date('Y'); ?></span>
	  		</a>
      		<?php endif; ?>
		<article class="small"> 
			<h1 class="title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"> <?php the_title(); ?> </a> </h1>

			<div class="yoga-blog-category">
			<a class="au-icon" href="<?php echo esc_url(get_author_posts_url( get_the_author_meta( 'ID' ) ));?>"><?php esc_attr_e('By','yoga'); ?>
				<?php the_author(); ?>
				</a>
				
				<?php   $cat_list = get_the_category_list();
				if(!empty($cat_list)) { ?>
				<?php the_category(', '); ?>
				<?php } ?>
				
			</div>
			
				<?php
				$yoga_more = strpos( $post->post_content, '<!--more' );
				if ( $yoga_more ) :
					echo '<p>' . get_the_content() . '</p>';
				else :
					 echo '<p>' . get_the_excerpt() . '</p>';
				endif;
				?>
			
				<?php wp_link_pages( array( 'before' => '<div class="link">' . __( 'Pages:', 'yoga' ), 'after' => '</div>' ) ); ?>
		</article>
	</div>
</div>