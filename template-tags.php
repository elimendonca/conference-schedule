<?php

/*  Copyright 2011 Simon Wheatley

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
 * For use within a Participant loop, displays the sessions the current
 * participant is taking part in.
 *
 * @param string $before Optional. Before list.
 * @param string $sep Optional. Separate items using this.
 * @param string $after Optional. After list.
 * @return void (Echoes HTML)
 * @author Simon Wheatley
 **/
function the_sessions( $before = '', $sep = ', ', $after = '' ) {
	$sessions = get_the_sessions( $before, $sep, $after );
	echo apply_filters( 'cs_the_sessions', $sessions, $before, $sep, $after );
}

/**
 * For use within a Participant loop, retrieve a participants
 * sessions as a list with specified format. 
 *
 * @param string $before Optional. Before list.
 * @param string $sep Optional. Separate items using this.
 * @param string $after Optional. After list.
 * @return string The HTML for the sessions list
 * @author Simon Wheatley
 **/
function get_the_sessions( $before = '', $sep = ', ', $after = '' ) {
	global $post;
	$session_term = get_term_by( 'slug', $post->post_name, 'participants' );
	$session_ids = get_objects_in_term( $session_term->term_id, 'participants' );

	$output = '';
	$sessions = array(); 
	foreach ( $session_ids as $s )
		$sessions[] = "<a href='" . get_permalink( $s ) . "'>" . get_the_title( $s ) . "</a>";

	$output = apply_filters( "cs_session_links", $sessions, $session_ids );
	return $before . join( $sep, $sessions ) . $after;
}



/**
 * Whether session has a schedule set, for use within a Session loop.
 *
 * @return bool Returns true if the current session has a schedule
 * @author Simon Wheatley
 **/
function has_schedule() {
	return (bool) get_post_meta( get_the_ID(), '_cs_has_schedule', true );
}

/**
 * Display the time at which the session starts.
 *
 * @param string $time_format Optional Either 'G', 'U', or php date format, defaults to the value specified in this plugin's time_format option.
 * @param string $short_time_format Optional Either 'G', 'U', or php date format defaults to the value specified in this plugin's short_time_format option.
 * @return void (Echoes)
 */
function the_start_time( $time_format = null, $short_time_format = null ) {
	echo apply_filters( 'cs_the_start_time', get_the_start_time( $time_format, $short_time_format ), $time_format, $short_time_format );
}

/**
 * Retrieve the time at which the session starts.
 *
 * @param string $time_format Optional Either 'G', 'U', or php date format, defaults to the value specified in this plugin's time_format option.
 * @param string $short_time_format Optional Either 'G', 'U', or php date format defaults to the value specified in this plugin's short_time_format option.
 * @param int|object $post Optional post ID or object. Default is global $post object.
 * @return string
 */
function get_the_start_time( $time_format = null, $short_time_format = null, $post = null ) {
	$post = get_post( $post );
	$start = get_post_meta( $post->ID, '_cs_schedule_start', true );
	
	$the_start_time = cs_format_time( $start, $time_format, $short_time_format );

	return apply_filters( 'get_the_start_time', $the_start_time, $time_format, $short_time_format, $post );
}

/**
 * Display the time at which the session starts.
 *
 * @param string $time_format Optional Either 'G', 'U', or php date format, defaults to the value specified in this plugin's time_format option.
 * @param string $short_time_format Optional Either 'G', 'U', or php date format defaults to the value specified in this plugin's short_time_format option.
 * @return void (Echoes)
 */
function the_end_time( $time_format = null, $short_time_format = null ) {
	echo apply_filters( 'cs_the_end_time', get_the_end_time( $time_format, $short_time_format ), $time_format, $short_time_format );
}

/**
 * Retrieve the time at which the session starts.
 *
 * @param string $time_format Optional Either 'G', 'U', or php date format, defaults to the value specified in this plugin's time_format option.
 * @param string $short_time_format Optional Either 'G', 'U', or php date format defaults to the value specified in this plugin's short_time_format option.
 * @param int|object $post Optional post ID or object. Default is global $post object.
 * @return string
 */
function get_the_end_time( $time_format = null, $short_time_format = null, $post = null ) {
	$post = get_post( $post );
	$end = get_post_meta( $post->ID, '_cs_schedule_end', true );
	
	$the_end_time = cs_format_time( $end, $time_format, $short_time_format );

	return apply_filters( 'get_the_end_time', $the_end_time, $time_format, $short_time_format, $post );
}

/**
 * Utility function which handles the date formatting for the various schedule star
 * and end time template tags.
 *
 * @param int $timestamp A UNIX timestamp
 * @param string $time_format Optional Either 'G', 'U', or php date format, defaults to the value specified in this plugin's time_format option.
 * @param string $short_time_format Optional Either 'G', 'U', or php date format defaults to the value specified in this plugin's short_time_format option.
 * @return string A formatted date string
 * @author Simon Wheatley
 **/
function cs_format_time( $timestamp, $time_format, $short_time_format ) {
	global $conf_sched;

	if ( $time_format == null )
		$time_format = $conf_sched->get_option( 'time_format' );
	if ( $short_time_format == null )
		$short_time_format = $conf_sched->get_option( 'short_time_format' );

	if ( ! (int) date( 'i', $timestamp ) ) {
		$formatted_time = date( $short_time_format, $timestamp );
	} else {
		$formatted_time = date( $time_format, $timestamp );
	}

	return $formatted_time;
}

	
	/**
	 * Retrieves all the sessions for that conference
	 *
	 * @return array of sessions
	 * @author Ana Aires
	 **/
	function get_sessions()
	{
		global $query;
		$sessions = query_posts( $query . '&post_type=session&post_status=publish&posts_per_page=-1' );
		return $sessions;
	}
	
	/**
	 * Retrieves all the sessions for that conference for a specific day
	 * order by schedule start
	 *
	 * @return array of sessions
	 * @author Ana Aires
	 **/
	function get_sessions_at($day)
	{ 
		$result = array();
		
		$sessions = query_posts(array(
                'post_type' => 'session',
				'post_status' => 'publish',
			    'posts_per_page' => -1,
				'orderby' => 'meta_value_num',
				'meta_key' => '_cs_schedule_start',
				'order' => 'ASC'
		));
	
		foreach ($sessions as $session) {
			$meta = get_metadata('post', $session->ID);
			
			$date = (int) $meta['_cs_schedule_start'][0]; 
			
				$d = date('d', $date);
				if($d == $day)
					$result[] = $session;
			
		}
		return $result;
	}
	
	/**
	 * Given session id return its location
	 * @param unknown_type $session_id
	 * @return string
	 * @author Ana Aires
	 */
	function get_session_location($session_id)
	{
		$location = -1;
		$terms = get_the_terms( $session_id, 'locations' );
		if(!empty($terms))
			foreach ($terms as $t)
				return $t->name; //we only need the firts result
		
		return $location;
	}
	
	/**
	 * Given session id returns session title
	 * @param int $session_id
	 * @return string
	 * @author Ana Aires
	 */
	function get_session_title($session_id)
	{
		$session = get_post($session_id);
	
		
		if($session)
			$session_url = '<a href="'.get_permalink($session_id).'">'.$session->post_title.'</a>';	 
		else 
			return 'no title available';
			
		
			
		return $session_url;
		
	}
	
	/**
	 * Give a session id collects the session slot like start-stop
	 * where start and stop its hour:min
	 * @param unknown_type $session_id
	 * @return string
	 * @author Ana Aires
	 */
	function get_session_slot($session_id)
	{ 
			
		$start = (int) get_post_meta( $session_id, '_cs_schedule_start', true ); 
		$end = (int) get_post_meta( $session_id, '_cs_schedule_end', true );
		$has_schedule = (bool) get_post_meta( $session_id, '_cs_has_schedule', true );
		if ( ! $has_schedule ) {
				return null;
		}
		
		$short_format = '';
		$start_long = esc_attr( date( DATE_RFC2822, $start ) );
		$end_long = esc_attr( date( DATE_RFC2822, $end ) );
		if ( ! (int) date( 'i', $start ) ) 
			$start_short = esc_attr( date( 'ga', $start ) );
		else 
			$start_short = esc_attr( date( 'g.ia', $start ) );
		if ( ! (int) date( 'i', $end ) ) 
			$end_short = esc_attr( date( 'ga', $end ) );
		else 
			$end_short = esc_attr( date( 'g.ia', $end ) );
			
			
			return $start_short.' - '.$end_short;
				
	}
	
	/**
	 * 
	 * Given session id returns html code to display speaker name with link to single session page
	 * @param int $session_id
	 * @return string
	 * @author Ana Aires
	 */
	function get_session_speaker($session_id)
	{
		
		$speaker = 'not defined';
		$terms = get_the_terms( $session_id, 'participants' ); 
		if(!empty($terms))
			foreach ($terms as $t) 
			{		
				$post_id =$t->object_id;
			
				$participant = get_speaker_by_title($t->name);
				$participanturl = get_permalink($participant->ID);
				
				
				$thumbid = get_post_thumbnail_id($participant->ID);
				
			
				$image = wpconference_vt_resize( $thumbid, '', 35, 35, true );
				
				echo '<a href="'.$participanturl.'"><img class="sprogram" src="'.$image[url].'"></img></a>';
				
				$speaker = '<a href="'.get_permalink($participant->ID).'">'.$t->name.'</a>';

				
				return $speaker; //we only need the firts result
			}
		return $speaker;
	}
	
	/**
	 * 
	 * Given a speaker name collects the post object participant. Its usefull
	 * specially because the connection between participants as taxonomy and participants as post type
	 * @param string $page_title
	 * @param $output
	 * @return object or null if non existent.
	 * @author Ana Aires
	 */
	function get_speaker_by_title($page_title, $output = OBJECT) {
    global $wpdb;
        $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='participant'", $page_title ));
        if ( $post )
            return get_post($post, $output);

    return null;
	}
	
	
	/**
	 * 
	 * Determine if there is any schedule sessions yet or not
	 * 
	 * @return true if there is ate least one session false otherwise
	 * @author Ana Aires
	 */
	function has_sessions()
	{
		$sessions = query_posts( $query . '&post_type=session&post_status=publish&posts_per_page=1' );
		if(count($sessions >0))
			return true;
		return false;
	}
	

?>