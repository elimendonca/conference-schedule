<?php
/**
 * The loop that displays a single participant.
 *
 * This can be overridden in child themes with loop-single.php.
 *
 */
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<?php if ( $referer = @ $_GET[ 'cs_referer' ] ) : ?>
					<div id="nav-above" class="navigation">
						<div class="nav-previous"><a href="<?php echo esc_attr( $referer ); ?>"><?php _e( '&larr; Back', 'conf_sched' ); ?></a></div>
					</div><!-- #nav-above -->
				<?php endif; ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'participant' ); ?>
					<?php endif; ?>

					<h1 class="entry-title"><?php the_title(); ?></h1>
					
					<?php the_terms( get_the_ID(), 'participant-roles', '<div class="cs-roles">Roles: ', ', ', '</div>' ); ?>

					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
					
					<?php edit_post_link( __( 'Edit', 'twentyten' ), '<div class="entry-utility"><span class="edit-link">', '</span></div><!-- .entry-utility -->' ); ?>
					
				</div><!-- #post-## -->

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><a href="/speakersparticipants/">&larr; Back to Speakers &amp; Participants</a></div>
				</div><!-- #nav-below -->

				<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>