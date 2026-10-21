<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package yoga
 */
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<div class="col-md-12">
<div id="comments" class="comments-area yoga-card-box padding-20">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) : ?>
		<div class="yoga-heading-bor-bt">
		<h5 class="comments-title">
			<?php
				$comments_number = get_comments_number();
				printf( // WPCS: XSS OK.
					esc_html(
						// translators: %1s: comments number, %2s: post title
						_nx( 
							'%1$1s Comment for &ldquo;%2$2s&rdquo;',
							'%1$1s Comments for &ldquo;%2$2s&rdquo;',
							$comments_number,
							'comments title',
							'yoga'
					   )
					),
					number_format_i18n( $comments_number ),
					get_the_title()
				);
			?>
		</h5>
		</div>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'yoga' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'yoga' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'yoga' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-above -->
		<?php endif; // Check for comment navigation. ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'      => 'ol',
					'short_ping' => true,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
			<h5 class="screen-reader-text"><?php esc_attr_e( 'Comment navigation', 'yoga' ); ?></h5>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'yoga' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'yoga' ) ); ?></div>

			</div><!-- .nav-links -->
		</nav><!-- #comment-nav-below -->
		<?php
		endif; // Check for comment navigation.
	endif; // Check for have_comments().
	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() ) : ?>

		<p class="no-comments"><?php esc_attr_e( 'Comments are closed.', 'yoga' ); ?></p>
	<?php
	endif;
	comment_form();
	?>
</div><!-- #comments -->
</div>