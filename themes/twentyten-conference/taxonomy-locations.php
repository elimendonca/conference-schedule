<?php
	/**
	 * The template for displaying the schedule for a location.
	 */

	get_header(); ?>

			<div id="container" class="one-column">
				<div id="content" role="main">

<?php
	/* Queue the first post, that way we know
	 * what date we're dealing with (if that is the case).
	 *
	 * We reset this later so we can run the loop
	 * properly with a call to rewind_posts().
	 */
	if ( have_posts() )
		the_post();
?>

<?php  // @TODO Sort out the globals below into a template tag ?>
<h1 class="page-title">Schedule for <?php single_term_title(); ?> <?php global $wp_query; if ( $wp_query->max_num_pages > 1 ) : ?>, page <?php global $page; echo $page; ?> of <?php echo $wp_query->max_num_pages; endif; ?></h1>

<p><a href="<?php echo esc_attr( get_post_type_archive_link( 'session' ) ); ?>">&larr; View entire schedule</a></p>

<?php
	/* Since we called the_post() above, we need to
	 * rewind the loop back to the beginning that way
	 * we can run the loop properly, in full.
	 */
	rewind_posts();

	/* Run the loop for the archives page to output the posts.
	 * If you want to overload this in a child theme then include a file
	 * called loop-archive.php and that will be used instead.
	 */
	 get_template_part( 'loop', 'session' );
?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_footer(); ?>
