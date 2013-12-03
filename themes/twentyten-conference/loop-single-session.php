<?php
/**
 * The loop that displays a single participant.
 *
 * This can be overridden in child themes with loop-single.php.
 *
 */
// @TODO: Add microformats for hcards and hcal
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="nav-above" class="navigation">
					<div class="nav-previous"><a href="<?php echo esc_attr( get_post_type_archive_link( 'session' ) ); ?>">&larr; View entire schedule</p>
					</div>
				</div><!-- #nav-above -->

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<h1 class="entry-title"><?php the_title(); ?></h1>
					
					<div class="session-meta">
						<span class="location"><?php the_terms( get_the_ID(), 'locations' ); ?></span><?php if ( $has_schedule = get_post_meta( get_the_ID(), '_cs_has_schedule', true ) ) : ?>,
							<span class="schedule">
								<?php $start = get_post_meta( get_the_ID(), '_cs_schedule_start', true ); ?>
								<?php $end = get_post_meta( get_the_ID(), '_cs_schedule_end', true ); ?>
								<span class="session-start" title="<?php echo esc_attr( date( DATE_RFC2822, $start ) ); ?>">
									<?php if ( ! (int) date( 'i', $start ) ) : ?>
										<?php echo esc_html( date( 'ga', $start ) ); ?>
									<?php else : ?>
										<?php echo esc_html( date( 'g.ia', $start ) ); ?>
									<?php endif; ?>
								</span> - 
								<span class="session-end" title="<?php echo esc_attr( date( DATE_RFC2822, $end ) ); ?>">
									<?php if ( ! (int) date( 'i', $end ) ) : ?>
										<?php echo esc_html( date( 'ga', $end ) ); ?>
									<?php else : ?>
										<?php echo esc_html( date( 'g.ia', $end ) ); ?>
									<?php endif; ?>
								</span>
							</span>
						<?php endif; ?>
					</div>

					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
					
					<?php edit_post_link( __( 'Edit', 'twentyten' ), '<div class="entry-utility"><span class="edit-link">', '</span></div><!-- .entry-utility -->' ); ?>
					
				</div><!-- #post-## -->

				<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>