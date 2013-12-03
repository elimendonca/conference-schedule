<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>

<?php if( $participants->have_posts() ) : ?>
	<div class="participants"> 
			<?php global $post; while ( $participants->have_posts() ) : $participants->the_post(); ?>
			<div <?php post_class( 'participant equal_height vcard participant-' . $post->post_name ); ?> id="participant-<?php the_ID(); ?>">
				<h3 class="participant-title">
					<a href="<?php echo esc_attr( add_query_arg( array( 'cs_referer' => urlencode( $_SERVER['REQUEST_URI'] ) ), get_permalink() ) ); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
							
				<?php  global $post; if ( has_post_thumbnail($post->ID) ) :
							$thumb = get_post_thumbnail_id();
							
							
							global $conf_sched;

							$width = $conf_sched->get_option('speakers-width',80);
							$height = $conf_sched->get_option('speakers-height',80);
							
							$image = wpconference_vt_resize( $thumb, '', $width, $height, true );
						?>
							<img title="<?php the_title(); ?>" src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
		
						<?php endif; ?><br/>
						<span class="fn"><?php the_title(); ?></span>
					</a>
				</h3>
				<?php the_excerpt();?>
			</div>
 		<?php endwhile; ?>
	</div>
<?php else : ?>
	<p class="no-participants">No participants booked yet.</p>
<?php endif; ?>
<div class="clear"><!--  --></div>
