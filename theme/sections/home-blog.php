<?php $yoga_news_enable = get_theme_mod('yoga_news_enable','true');
$disable_news_meta = get_theme_mod('disable_news_meta','false');

$yoga_news_subtitle = get_theme_mod('yoga_news_subtitle');

	if($yoga_news_enable !='false')
	{ $yoga_total_posts = get_option('posts_per_page'); /* number of latest posts to show */
	
	if( !empty($yoga_total_posts) && ($yoga_total_posts > 0) ):
	?>

<!--==================== BLOG SECTION ====================-->
<section id="blog" class="yoga-blog-section">
	<div class="overlay">
    <div class="container">
      <div class="row">
        <div class="col-md-12 padding-bottom-50 text-center">
          <div class="yoga-heading">
            <?php $yoga_news_title = get_theme_mod('yoga_news_title');
          
            if( !empty($yoga_news_title) ):
              echo '<h3 class="yoga-heading-inner">'.esc_attr($yoga_news_title).'</h3>';
            endif;  
          
           if( !empty($yoga_news_subtitle) ):

              echo '<p class="subtitle">'.esc_attr($yoga_news_subtitle).'</p>';

            endif; ?> 
          </div>
        </div>
      </div>
      <div class="clear"></div>
      <div class="row">
        <div id="home-news">
        <?php $news_select = get_theme_mod('news_select',3);
			   $yoga_latest_loop = new WP_Query(array( 'post_type' => 'post', 'posts_per_page' => $news_select, 'order' => 'DESC','ignore_sticky_posts' => true, ''));
			    if ( $yoga_latest_loop->have_posts() ) :
			     while ( $yoga_latest_loop->have_posts() ) : $yoga_latest_loop->the_post();?>
       <div class="col-md-4">
          <div class="yoga-blog-post-box">
            <?php if(has_post_thumbnail()): ?>
            <a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" class="yoga-blog-thumb"> 
              <?php $defalt_arg =array('class' => "img-responsive"); ?>
              <?php the_post_thumbnail('', $defalt_arg); ?>
              <span class="yoga-blog-date"><?php echo get_the_date('M'); ?> <?php echo get_the_date('j,'); ?> <?php echo get_the_date('Y'); ?></span>
            </a>  
            <?php endif; ?>
            <article class="small">
              <h1 class="title"> <a href="<?php echo get_permalink() ?>" title="<?php echo get_the_title() ?>"><?php echo get_the_title() ?></a> </h1>
              <?php if($disable_news_meta !=true) {?>
			  <div class="yoga-blog-category">
                <a class="au-icon" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) );?>"><?php _e('By','yoga'); ?>
          <?php the_author(); ?>
          </a> 
                <?php $cat_list = get_the_category_list();
								if(!empty($cat_list)) { ?>
                <?php the_category(', '); ?>
                <?php } ?>
              </div>
			  <?php } ?>
            </article>
          </div>
        </div>
		<?php endwhile; endif;	wp_reset_postdata(); ?>
      </div>
        
      </div></div>
    </div>
    <!-- /.container --> 
  </div>
</section>
<?php endif; ?>
<?php } ?>