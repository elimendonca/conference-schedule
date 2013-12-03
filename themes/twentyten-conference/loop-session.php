<?php
/**
 * The loop that displays the schedule.
 *
 */
?>

<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div id="nav-above" class="navigation">
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Previous page', 'twentyten' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Next page <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
	</div><!-- #nav-above -->
<?php endif; ?>

<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
		<div class="entry-content">
			<p><?php _e( 'Apologies, but the schedule has not yet been published.', 'twentyten' ); ?></p>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php endif; ?>

<?php
	/* Start the Loop.
	 */ ?>
<?php if ( have_posts() ) : ?>

<table class="schedule">
	<thead>
		<tr>
			<th scope="col" id="session-time" class="column-session-time">Time</th>
			<th scope="col" id="session-info" class="column-session-time">Session</th>
			<th scope="col" id="session-with" class="column-session-with">With…</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope="col" class="column-session-time">Time</th>
			<th scope="col">Session</th>
			<th scope="col">With…</th>
		</tr>
	</tfoot>
	<tbody>

	<?php while ( have_posts() ) : the_post(); ?>

		<tr id="session-<?php the_ID(); ?>" <?php post_class(); ?>>
			<th scope="row" class="column-session-time session-time">
				<?php if ( $has_schedule = get_post_meta( get_the_ID(), '_cs_has_schedule', true ) ) : ?>
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
				<?php else : ?>
					<em><?php _e( 'No time set', 'conf_sched' ); ?></em>
				<?php endif; ?>
			</th>
			<td class="column-session-info session-info">
				<h2 class="session-title entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
				<?php the_terms( get_the_ID(), 'locations', '<p class="location"><strong>', '', '</strong></p>' ); ?>
				<?php the_excerpt( 'More information' ); ?>
			</td>
			<td class="column-session-with session-with">
				<?php 
					$withs = wp_get_object_terms( get_the_ID(), 'participants' );
					foreach ( $withs as $w ) :
						$s = new WP_Query( array( 'pagename' => $w->slug, 'post_type' => 'participant' ) );
						if ( $s->have_posts() ) : while( $s->have_posts() ) : $s->the_post();
 				?>
					<p class="with vcard"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark" class="url"><?php the_post_thumbnail( 'participant-schedule-thumb' ); ?><span class="fn"><?php the_title(); ?></span></a></p>
				<?php endwhile; else : ?>
					<p><em>No participants.</em></p>
				<?php endif; endforeach; wp_reset_query();  ?>
			</td>
		</tr>

	<?php endwhile; // End the loop. Whew. ?>

	</tbody>
</table>

<?php endif; // Have posts ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
				</div><!-- #nav-below -->
<?php endif; ?>
