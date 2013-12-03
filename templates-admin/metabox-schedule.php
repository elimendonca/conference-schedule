<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>

<?php wp_nonce_field( 'cs_schedule', '_cs_schedule_nonce' ); ?>

<p>
	<label for="cs_has_schedule">
		<span title="<?php esc_attr_e( 'Select whether this session has a scheduled start and end time; n.b. on the front end schedule page, sessions are ordered by their start time.', $this->name ); ?>"><?php _e( 'Schedule', $this->name ); ?></span>
		<input type="checkbox" name="cs_has_schedule" value="1" id="cs_has_schedule" <?php checked( $has_schedule ); ?> />:
	</label>
	<?php  if ( count( $days ) > 1 ) : ?>
		<label for="cs_start_day" title="<?php echo esc_attr( __( 'Start day:', $this->name ) ); ?>">
			Start:
			<select id="cs_start_day" name="cs_start_day">
				<option value=""><?php _e( 'Select a day', $this->name ); ?></option>
				<?php foreach ( $days as & $d ) : ?>
					<option title="<?php echo esc_attr( date( 'l jS F, Y', $d ) ); ?>" value="<?php echo esc_attr( $d ); ?>" <?php selected( $d, $start_day ); ?>><?php echo esc_html( date( 'D j M', $d ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label for="cs_start_hour" title="<?php echo esc_attr( __( 'Start hour:', $this->name ) ); ?>">
			-
	<?php else : ?>
		<label for="cs_start_hour">
			Start:
			<input type="hidden" name="cs_start_day" value="<?php echo esc_attr( $days[ 0 ] ); ?>" />
	<?php endif; ?>
		<?php if ( count( $days ) == 1 ) : ?>
			<span title="<?php echo esc_attr( date( 'l jS F, Y', $days[ 0 ] ) ); ?>"><?php echo esc_html( date( 'D j M', $days[ 0 ] ) ); ?></span>
		<?php endif; ?>
		<select id="cs_start_hour" name="cs_start_hour">
			<option value=""> -- </option>
			<?php for ( $i = 0; $i < 24; $i++ ) : $hr = str_pad( $i, 2, '0', STR_PAD_LEFT ); ?>
				<option value="<?php echo esc_attr( $hr ); ?>" <?php selected( $hr, $start_hour ); ?>><?php echo esc_html( $hr ); ?></option>
			<?php endfor; ?>
		</select></label><label for="cs_start_minute" title="<?php echo esc_attr( __( 'Start minute: ', $this->name ) ); ?>">:<select id="cs_start_minute" name="cs_start_minute">
			<option value=""> -- </option>
			<?php for ( $i = 0; $i < 12; $i++ ) : $min = str_pad( ($i*5), 2, '0', STR_PAD_LEFT ); ?>
				<option value="<?php echo esc_attr( $min ); ?>" <?php selected( $min, $start_minute ); ?>><?php echo esc_html( $min ); ?></option>
			<?php endfor; ?>
		</select>
	</label>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<?php if ( count( $days ) > 1 ) : ?>
		<label for="cs_end_day" title="<?php echo esc_attr( __( 'End day:', $this->name ) ); ?>">
			End:
			<select id="cs_end_day" name="cs_end_day">
				<option value=""><?php _e( 'Select a day', $this->name ); ?></option>
				<?php foreach ( $days as & $d ) : ?>
					<option title="<?php echo esc_attr( date( 'l jS F, Y', $d ) ); ?>" value="<?php echo esc_attr( $d ); ?>" <?php selected( $d, $end_day ); ?>><?php echo esc_html( date( 'D j M', $d ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<label for="cs_end_hour" title="<?php echo esc_attr( __( 'End hour:', $this->name ) ); ?>">
			-
	<?php else : ?>
		<label for="cs_end_hour" title="<?php echo esc_attr( __( 'End hour:', $this->name ) ); ?>">
			End:
			<input type="hidden" name="cs_end_day" value="<?php echo esc_attr( $days[ 0 ] ); ?>" />
	<?php endif; ?>
		<?php if ( count( $days ) == 1 ) : ?>
			<span title="<?php echo esc_attr( date( 'l jS F, Y', $days[ 0 ] ) ); ?>"><?php echo esc_html( date( 'D j M', $days[ 0 ] ) ); ?></span>
		<?php endif; ?>
		<select id="cs_end_hour" name="cs_end_hour">
			<option value="" <?php selected( '-', $end_hour ); ?>> -- </option>
			<?php for ( $i = 0; $i < 24; $i++ ) : $hr = str_pad( $i, 2, '0', STR_PAD_LEFT ); ?>
				<option value="<?php echo esc_attr( $hr ); ?>" <?php selected( $hr, $end_hour ); ?>><?php echo esc_html( $hr ); ?></option>
			<?php endfor; ?>
		</select></label><label for="cs_end_minute" title="<?php echo esc_attr( __( 'End minute: ', $this->name ) ); ?>">:<select id="cs_end_minute" name="cs_end_minute">
			<option value=""> -- </option>
			<?php for ( $i = 0; $i < 12; $i++ ) : $min = str_pad( ($i*5), 2, '0', STR_PAD_LEFT ); ?>
				<option value="<?php echo esc_attr( $min ); ?>" <?php selected( $min, $end_minute ); ?>><?php echo esc_html( $min ); ?></option>
			<?php endfor; ?>
		</select>
	</label>
	
</p>
