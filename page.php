<?php

/**
 * The main template file
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
				<div class="post-single__content">
					<?php the_content(); ?>
				</div>
			<?php endwhile; // End of the loop.?>

<?php get_footer();
