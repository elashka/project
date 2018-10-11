<?php

/**
 * The single template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 */
?>
<?php get_header(); ?>
			<?php while ( have_posts() ) : the_post(); ?>
                <div class="post-single">
                    <div class="post-single__title mb20">
	                    <?php the_title( '<h1>', '</h1>' ); ?>
                        <div class="post-date">
                            <span><?php echo get_the_date( 'D d M Y' ) . ' ' . __( 'at', 'es-project') . ' ' . get_the_time( 'H:i' ); ?></span>
                        </div>
                    </div>
                    <div class="post-single__image">
                        <?php the_post_thumbnail('single-posts-thm'); ?>
                    </div>
                    <div class="post-single__content">
						<?php the_content(); ?>
                    </div>
                    <div class="post-tags">
                        <?php the_tags( __( 'Tags:', 'es-project' ), ' | ' ); ?>
                    </div>
					<?php
					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
					?>
                </div>
			<?php endwhile; // End of the loop.?>
            <div class="sidebar">
				<?php if ( is_active_sidebar( 'posts-sidebar-right' ) ) {
					dynamic_sidebar( 'posts-sidebar-right' );
				} ?>
            </div>
<?php get_footer();
