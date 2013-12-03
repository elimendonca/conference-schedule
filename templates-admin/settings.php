<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>

<div class="wrap" id="<?php echo esc_attr( $this->name ); ?>-options">

	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e( 'Conference Settings', $this->name ); ?></h2>
	
		
	<h3><?php _e( 'Settings', $this->name ); ?></h3>

	
	<form method="post" action="options.php">
		<?php settings_fields( $this->name ); ?>

		<table class="form-table">
			<?php // @TODO: Show dates above in human readable format, and ask "is this right?" ?>
			<tr valign="top"><th scope="row"><?php _e( 'Conference days', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[days]" value="<?php echo esc_attr( $options['days'] ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Please enter the days over which your conference runs, separating multiple days with a comma (<kbd>,</kbd>) and using this format: <kbd>YYYY-MM-DD,YYYY-MM-DD</kbd>', $this->name ) ?></span>
				</td>
			</tr>
			
			<tr valign="top"><th scope="row"><?php _e( 'Time Format', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[time_format]" value="<?php echo esc_attr( $options['time_format'] ); ?>" class="small-text" /> <span class="example"><?php echo esc_html( date( $options['time_format'] ) ); ?></span><br />
					<span class="description"><?php _e( 'This is the date format used for schedule times which are not exactly on the hour.', $this->name ) ?></span>
				</td>
			</tr>
			<tr valign="top"><th scope="row"><?php _e( 'Short Time Format', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[short_time_format]" value="<?php echo esc_attr( $options['short_time_format'] ); ?>" class="small-text" /> <span class="example"><?php echo esc_html( date( $options['short_time_format'] ) ); ?></span><br />
					<span class="description"><?php _e( 'This is the date format used for schedule times which <strong>are</strong> exactly on the hour.', $this->name ) ?></span><br />
					<a href="http://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date and time formatting</a>
				</td>
			</tr>
			
			
			<!-- TagLine Days -->
			<tr valign="top"><th scope="row"><?php _e( 'Conference Tagline Days', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[tagline-days]" value="<?php echo esc_attr( $options['tagline-days'] ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Please enter your conference days, this will be part of the Header. For instance: October 26-28 2011', $this->name ) ?></span>
				</td>
			</tr>
			<!-- Tagline Location -->
			<tr valign="top"><th scope="row"><?php _e( 'Conference Tagline Location', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[tagline-location]" value="<?php echo esc_attr( $options['tagline-location'] ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Please enter your conference location, this will be part of the Header. For instance: Lisbon, Portugal', $this->name ) ?></span>
				</td>
			</tr>
			
			<!-- Conference Small Description -->
			<tr valign="top"><th scope="row"><?php _e( 'Conference Description', $this->name ); ?></th>
				<td>
					<textarea  ROWS="3" COLS="50" name="<?php echo esc_attr( $this->name ); ?>[description]" class="regular-text"><?php echo esc_attr( $options['description'] ); ?></textarea><br />
					<span class="description"><?php _e( 'Please enter your conference small description to be part of the Header.', $this->name ) ?></span>
				</td>
			</tr>
			
			<!-- Speakers Category -->
			<tr valign="top"><th scope="row"><?php _e( 'Speakers Category', $this->name ); ?></th>
				<td>
					<?php wp_dropdown_categories(array('hide_empty'=>0,'taxonomy'=>'participant-roles',id=>'cat_speakers',  'show_option_none'   => '---', 'name' => esc_attr( $this->name )."[speakers-category]", 'selected' => esc_attr( $options['speakers-category']))); ?> <br />
					<span class="description"><?php _e( 'Please choose which category is your speakers category. If none is available please go to Participants menu and add a new role speakers', $this->name ) ?></span>
				</td>
			</tr>
			
			
			<!-- Speakers Homepage Width-->
			<tr valign="top"><th scope="row"><?php _e( 'Speakers Image width', $this->name ); ?></th>
				<td>
					<input  type="text" name="<?php echo esc_attr( $this->name ); ?>[speakers-width]" value="<?php echo esc_attr( $options['speakers-width'] ); ?>" class="small-text" /><br />
					<span class="description"><?php _e( 'Please enter speakers width for homepage image. Default value 80px.', $this->name ) ?></span>
				</td>
			</tr>
			
			<!-- Speakers Homepage height-->
			<tr valign="top"><th scope="row"><?php _e( 'Speakers Image height', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[speakers-height]" value="<?php echo esc_attr( $options['speakers-height'] ); ?>" class="small-text" /><br />
					<span class="description"><?php _e( 'Please enter speakers height for homepage image. Default value 80px.', $this->name ) ?></span>
				</td>
			</tr>
			
			
			<!-- Conference Logo -->
			<tr valign="top"><th scope="row"><?php _e( 'Custom Logo', $this->name ); ?></th>
				<td>
					<input id="upload_image" type="text" size="36" name="<?php echo esc_attr( $this->name ); ?>[upload-logo]" value="<?php echo esc_attr( $options['upload-logo'] ); ?>" />
					<input id="upload_image_button" type="button" value="Upload Image" />
						<br />
					<span class="description"><?php _e( 'Upload a logo for your theme. Expected size 200x180px', $this->name ) ?></span>
				</td>
			</tr>
			
			
			<!-- copyright name-->
			<tr valign="top"><th scope="row"><?php _e( 'Name for Copyrigh on footer', $this->name ); ?></th>
				<td>
					<input  type="text" name="<?php echo esc_attr( $this->name ); ?>[copyright-name]" value="<?php echo esc_attr( $options['copyright-name'] ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Please enter year for footer copyrigh. If not set copyright will be disbaled.', $this->name ) ?></span>
				</td>
			</tr>
			
			
			<!-- copyright year-->
			<tr valign="top"><th scope="row"><?php _e( 'Year for Copyrigh on footer', $this->name ); ?></th>
				<td>
					<input  type="text" name="<?php echo esc_attr( $this->name ); ?>[copyright-year]" value="<?php echo esc_attr( $options['copyright-year'] ); ?>" class="small-text" /><br />
					<span class="description"><?php _e( 'Please enter year for footer copyrigh. Default value 2012.', $this->name ) ?></span>
				</td>
			</tr>
			
			<!-- Footer Color-->
			<tr valign="top"><th scope="row"><?php _e( 'Footer Color', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[footer-color]" value="<?php echo esc_attr( $options['footer-color'] ); ?>" class="small-text" /><br />
					<span class="description"><?php _e( 'Please enter color code for footer. Default value #111111.', $this->name ) ?></span>
				</td>
			</tr>
			

			<!-- headings and links color-->
			<tr valign="top"><th scope="row"><?php _e( 'Headings and links color', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[ha-color]" value="<?php echo esc_attr( $options['ha-color'] ); ?>" class="small-text" /><br />
					<span class="description"><?php _e( 'Please enter color code for footer. Default value #3399FF.', $this->name ) ?></span>
				</td>
			</tr>
			
			
			<!-- table odd row background color-->
			<tr valign="top"><th scope="row"><?php _e( 'Odd row background color for tables', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[trodd-color]" value="<?php echo esc_attr( $options['trodd-color'] ); ?>" class="small-text" /><br />
					<span class="description"><?php _e( 'Please enter color code for footer. Default value #FFFFFF.', $this->name ) ?></span>
				</td>
			</tr>
			<!-- table even row background color-->
			<tr valign="top"><th scope="row"><?php _e( 'Even row background color for tables', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[treven-color]" value="<?php echo esc_attr( $options['treven-color'] ); ?>" class="small-text" /><br />
					<span class="description"><?php _e( 'Please enter color code for footer. Default value #A3D6F5.', $this->name ) ?></span>
				</td>
			</tr>
		
				
			<!-- eventbrite event id-->
			<tr valign="top"><th scope="row"><?php _e( 'Event id on EventBrite', $this->name ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( $this->name ); ?>[event-id]" value="<?php echo esc_attr( $options['event-id'] ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e( 'Please enter your event id on EventBrite. If none is provided then no registration will be available.', $this->name ) ?></span>
				</td>
			</tr>
			
			
		</table>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>

	</form>
	
</div>
