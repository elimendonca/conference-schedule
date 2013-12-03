<?php

/*  Copyright 2010 Simon Wheatley

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

/**
 * Hooks the Conference Scheduler custom action cs_pre_speakers_shortcode to:
 * * Remove the excerpt hook that TwentyTen put in
 *
 * @param  
 * @return void
 * @author Simon Wheatley
 **/
function my_cs_pre_participants_shortcode() {
	remove_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );
}
add_action( 'cs_pre_participants_shortcode', 'my_cs_pre_participants_shortcode' );

/**
 * Hooks the Conference Scheduler custom action cs_pre_speakers_shortcode to:
 * * Remove the excerpt hook that TwentyTen put in
 *
 * @param  
 * @return void
 * @author Simon Wheatley
 **/
function my_cs_post_participants_shortcode() {
	add_filter( 'get_the_excerpt', 'twentyten_custom_excerpt_more' );
}
add_action( 'cs_post_participants_shortcode', 'my_cs_post_participants_shortcode' );

?>