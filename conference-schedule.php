<?php

/*
Plugin Name: Conference Schedule
Plugin URI: http://simonwheatley.co.uk/wordpress/conference-schedule
Description: Create a conference website, with speakers, timetable and locations.
Version: 0.52
Author: Simon Wheatley
Author URI: http://simonwheatley.co.uk/wordpress/
*/
 
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

require_once( 'plugin.php' );
require_once( 'template-tags.php' );

/**
 * 
 * 
 * @package ConferenceScheduler
 * @author Simon Wheatley
 **/
class ConfSched extends ConfSched_Plugin {
	
	/**
	 * The version as far as rewrites are concerned, 
	 * we examine this to know when to flush rewrites.
	 *
	 * @var int
	 **/
	protected $rewrite_version;
	
	/**
	 * The version as far as DB changes are concerned.
	 *
	 * @var int
	 **/
	protected $db_version;
	
	/**
	 * Whether we are syncing a term between custom 
	 * post type and custom taxonomy.
	 *
	 * @var boolean
	 **/
	protected $syncing_term;
	
	/**
	 * Initiate!
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function __construct() {
		$this->setup( 'conf_sched' );
		if ( is_admin() ) {
			$this->add_action( 'admin_init' );
			$this->add_action( 'admin_menu' );
			$this->add_action( 'admin_notices' );
			$this->add_action( 'load-edit.php', 'load_edit' );
			$this->add_action( 'load-settings_page_conf_sched', 'load_settings' );
			$this->add_action( 'manage_participant_posts_custom_column', 'manage_participant_column_content', null, 2 );
			$this->add_action( 'manage_session_posts_custom_column', 'manage_session_column_content', null, 2 );
			$this->add_action( 'save_post', null, null, 2 );
			$this->add_filter( 'manage_participant_posts_columns', 'manage_participant_columns', null, 2 );
			$this->add_filter( 'manage_session_posts_columns', 'manage_session_columns', null, 2 );
			$this->add_filter( 'pre_insert_term', null, null, 2 );
			$this->add_meta_box( 'cs_schedule_metabox', __( 'Schedule', $this->name ), 'schedule_metabox', 'session', 'normal', 'core' );
		}
		
		$this->add_action( 'init' );
		$this->add_filter( 'request' );
		$this->add_shortcode( 'participants', 'shortcode_participants' );
		// $this->add_filter( 'posts_request' ); // Handy for debugging
		$this->rewrite_version = 4;
		$this->db_version = 2;
		$this->syncing_term = false;
	}
	
	/**
	 * Handy for debugging.
	 *
	 * @param string $req The MySQL for the WP query 
	 * @return string The MySQL for the WP query 
	 * @author Simon Wheatley
	 **/
	public function posts_request( $req ) {
		error_log( "Req: $req" );
		return $req;
	}
	
	// HOOKS AND ALL THAT
	// ==================

	/**
	 * Hooks the WP request filter to:
	 * * Inject some query vars to force session archive pages to order by start time
	 *
	 * @param  
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function request( $qv ) {  
		// If all the below are true, then this is the archive page for 
		// the 'session' custom post type.
		if ( count( $qv ) == 1 && isset( $qv[ 'post_type' ] ) && $qv[ 'post_type' ] == 'session' ) {
			
			// @TODO: Allow admin to order by meta_value_num THEN menu_order
			$qv[ 'orderby' ] = 'meta_value_num';
			$qv[ 'meta_key' ] = '_cs_schedule_start';
			$qv[ 'order' ] = 'ASC';
		}
		// Redirect all archive pages for custom taxonmy 'participants' to the
		// equivalent post in the custom post type.
		if ( count( $qv ) == 1 && isset( $qv[ 'participants' ] ) ) {
			wp_redirect( home_url( "/participant/" . $qv[ 'participants' ] . "/" ) );
			exit;
		}
		return $qv;
	}

	/**
	 * Hooks the WP init function to:
	 * * Register a custom post type for Participants
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function init() {
		$args = array(
			'can_export' => true,
			'capability_type' => 'page',
			'description' => __(  'The participants, workshop leaders and panellists, etc, for this conference.', $this->name ),
			'has_archive' => false,
			'hierarchical' => true,
			'menu_icon' => '',
			'menu_position' => 20,
			'public' => true,
			'rewrite' => array( 'slug' => 'participant', 'with_front' => false ),
			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'excerpt' ),
		);
		$args[ 'labels' ] = array(
			'name' => __( 'Participants', $this->name ),
			'singular_name' => __( 'Participant', $this->name ),
			'add_new' => __( 'Add New', $this->name ),
			'add_new_item' => __( 'Add New Participant', $this->name ),
			'edit_item' => __( 'Edit Participant', $this->name ),
			'new_item' => __( 'New Participant', $this->name ),
			'view_item' => __( 'View Participant', $this->name ),
			'search_items' => __( 'Search Participants', $this->name ),
			'not_found' => __( 'No participants found.', $this->name ),
			'not_found_in_trash' => __( 'No participants found in Trash.', $this->name ),
			'parent_item_colon' => __( 'Parent participant:', $this->name ),
		);
		register_post_type( 'participant', $args );
		$labels = array(
			'name' => __( 'Participants', $this->name ),
			'singular_name' => __( 'Participant', $this->name ),
			'search_items' => __( 'Search Participants', $this->name ),
			'popular_items' => __( 'Popular Participants', $this->name ),
			'all_items' => __( 'All Participants', $this->name ),
			'edit_item' => __( 'Edit Participant', $this->name ),
			'update_item' => __( 'Update Participant', $this->name ),
			'add_new_item' => __( 'Add New Participant', $this->name ),
			'new_item_name' => __( 'New Participant Name', $this->name ),
			'separate_items_with_commas' => __( 'Separate participants with commas', $this->name ),
			'add_or_remove_items' => __( 'Add or remove participants', $this->name ),
			'choose_from_most_used' => __( 'Choose from the most used participants', $this->name ),
		);
		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
		);
		register_taxonomy( 'participants', 'session', $args );
		$args = array(
			'can_export' => true,
			'capability_type' => 'page',
			'description' => __(  'The sessions for this conference.', $this->name ),
			'has_archive' => 'schedule',
			'hierarchical' => true,
			'menu_icon' => '',
			'menu_position' => 20,
			'public' => true,
			'rewrite' => array( 'slug' => 'session', 'with_front' => false ),
			'supports' => array( 'title', 'editor', 'page-attributes', 'excerpt' ),
		);
		$args[ 'labels' ] = array(
			'name' => __( 'Sessions', $this->name ),
			'singular_name' => __( 'Session', $this->name ),
			'add_new' => __( 'Add New', $this->name ),
			'add_new_item' => __( 'Add New Session', $this->name ),
			'edit_item' => __( 'Edit Session', $this->name ),
			'new_item' => __( 'New Session', $this->name ),
			'view_item' => __( 'View Session', $this->name ),
			'search_items' => __( 'Search Sessions', $this->name ),
			'not_found' => __( 'No sessions found.', $this->name ),
			'not_found_in_trash' => __( 'No sessions found in Trash.', $this->name ),
			'parent_item_colon' => __( 'Parent session:', $this->name ),
		);
		register_post_type( 'session', $args );
		$labels = array(
			'name' => __( 'Role', $this->name ),
			'singular_name' => __( 'Role', $this->name ),
			'search_items' => __( 'Search Roles', $this->name ),
			'popular_items' => __( 'Popular Roles', $this->name ),
			'all_items' => __( 'All Roles', $this->name ),
			'edit_item' => __( 'Edit Role', $this->name ),
			'update_item' => __( 'Update Role', $this->name ),
			'add_new_item' => __( 'Add New Role', $this->name ),
			'new_item_name' => __( 'New Role Name', $this->name ),
			'separate_items_with_commas' => __( 'Separate roles with commas', $this->name ),
			'add_or_remove_items' => __( 'Add or remove roles', $this->name ),
			'choose_from_most_used' => __( 'Choose from the most used roles', $this->name ),
		);
		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
		);
		register_taxonomy( 'participant-roles', 'participant', $args );
		$labels = array(
			'name' => __( 'Location', $this->name ),
			'singular_name' => __( 'Location', $this->name ),
			'search_items' => __( 'Search Locations', $this->name ),
			'popular_items' => __( 'Popular Locations', $this->name ),
			'all_items' => __( 'All Locations', $this->name ),
			'edit_item' => __( 'Edit Location', $this->name ),
			'update_item' => __( 'Update Location', $this->name ),
			'add_new_item' => __( 'Add New Location', $this->name ),
			'new_item_name' => __( 'New Location Name', $this->name ),
			'separate_items_with_commas' => __( 'Separate locations with commas', $this->name ),
			'add_or_remove_items' => __( 'Add or remove locations', $this->name ),
			'choose_from_most_used' => __( 'Choose from the most used locations', $this->name ),
		);
		$args = array(
			'hierarchical' => true,
			'labels' => $labels,
		);
		register_taxonomy( 'locations', 'session', $args );
		add_image_size( 'participant', 175, 175, true );
		add_image_size( 'participant-lineup', 130, 130, true );
		add_image_size( 'participant-schedule-thumb', 40, 40, true );
	}
	
	/**
	 * Hooks the WP admin_init action to:
	 * * Update the rewrite rules when necessary
	 *
	 * @param  
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function admin_init() {
		register_setting( $this->name, $this->name );
		if ( $this->get_option( 'rewrite_version', 0 ) < $this->rewrite_version ) {
			flush_rewrite_rules();
			$this->update_option( 'rewrite_version', $this->rewrite_version );
		}
		if ( $this->get_option( 'db_version', 0 ) < $this->db_version ) {
			$this->update_db();
			$this->update_option( 'db_version', $this->db_version );
			$this->clear_page_caches();
		}
		wp_enqueue_style( 'cs', $this->url( '/css/admin.css' ), null, '1.001' );
	}
	
	/**
	 * Hooks the WP load-edit.php to enqueue some CSS for non-hierachical post type 
	 * listing screens only.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function load_edit() {
		wp_enqueue_style( 'cs-edit', $this->url( '/css/admin-edit.css' ), null, '1.001' );
	}
	
	/**
	 * Hooks the dynamic WP load-settings_page_conf_sched action which
	 * fires when the settings page is loaded.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function load_settings() {
		if ( ! (bool) @ $_POST[ '_cs_copy_theme' ] )
			return;
		check_admin_referer( 'copy_theme', '_cs_copy_theme' );
		if ( ! $this->can_copy_theme() ) {
			$this->set_admin_error( sprintf( __( 'Sorry, I cannot write to your themes directory. Please try copying the files from the %s directory within this plugin manually into your WP themes directory.', $this->name ), '<kbd>twentyten-conference</kbd>' ) );
			return;
		}
		if ( $this->theme_present() ) {
			$this->set_admin_error( __( 'Your theme has already been copied.', $this->name ) );
			return;
		}
		$src = $this->dir( '/themes/twentyten-conference' );
		$dst = get_theme_root() . '/twentyten-conference';
		$this->recursive_copy( $src, $dst );
		$this->set_admin_notice( sprintf( __( 'The TwentyTen Conference theme has been copied to your themes directory, now <a href="%s">activate it</a>.', $this->name ), admin_url( 'themes.php' ) ) );
	}
	
	/**
	 * Hooks the WP admin_menu action to add items to the admin menu.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function admin_menu() {
		add_options_page( __( 'Conference Settings', $this->name ), __( 'Conference', $this->name ), 'manage_options', $this->name, array( & $this, 'settings' ) );
	}
	
	/**
	 * Hooks the WP admin_notices action to:
	 * * Show an explanatory notice on the participants taxonomy page
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function admin_notices() {
		$screen = get_current_screen();
		if ( $screen->base == 'edit-tags' && $screen->taxonomy == 'participants' ) {
			echo '<div id="message" class="updated"><p>';
			printf( __( 'Please <strong>do not add, edit or delete Participants here</strong>, instead edit them directly, using the links in the descriptions below, or from the <a href="%s">Participants</a> screen.', $this->name ), admin_url( 'edit.php?post_type=participant' ) );
			echo "</p></div>";
		}
		if ( ! $this->get_option( 'days' ) &&
		  	( $screen->id == 'settings_page_conf_sched' || $screen->post_type == 'session' ) ) {
			echo '<div id="message" class="updated"><p>';
			printf( __( 'Before you set up your schedule you must specify the "conference days" in the Conference Schedule <a href="%s">settings page</a>.', $this->name ), admin_url( 'options-general.php?page=conf_sched' ) );
			echo "</p></div>";
		}
	}

	/**
	 * Hooks the dynamic WP manage_session_posts_columns filter to add a custom 
	 * column at the left edge of the table.
	 *
	 * @param array $cols An array of column information 
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function manage_session_columns( $cols ) {
		$date = array_pop( $cols );
		$cols[ 'schedule' ] = 'Schedule';
		$cols[ 'location' ] = 'Location';
		$cols[ 'participants' ] = 'Participants';
		$cols[ 'date' ] = $date;
		return $cols;
	}

	/**
	 * Hooks the dynamic WP manage_session_posts_custom_column filter to add content to
	 * our custom column.
	 *
	 * @param  
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function manage_session_column_content( $col_name, $post_id ) {
		$terms = false;
		switch ( $col_name ) {
			case 'location' :
				$terms = get_the_terms( $post_id, 'locations' );
				$nothing = __( 'No location', $this->name );
				$this->show_terms_cell( $terms, $nothing );
				break;
			case 'participants' :
				$terms = get_the_terms( $post_id, 'participants' );
				$nothing = __( 'No participants', $this->name );
				$this->show_terms_cell( $terms, $nothing );
				break;
			case 'schedule' :
				$start = (int) get_post_meta( $post_id, '_cs_schedule_start', true ); 
				$end = (int) get_post_meta( $post_id, '_cs_schedule_end', true );
				$has_schedule = (bool) get_post_meta( $post_id, '_cs_has_schedule', true );
				if ( ! $has_schedule ) {
					_e( 'No schedule', $this->name );
					break;
				}
				$short_format = '';
				$start_long = esc_attr( date( DATE_RFC2822, $start ) );
				$end_long = esc_attr( date( DATE_RFC2822, $end ) );
				if ( ! (int) date( 'i', $start ) ) {
					$start_short = esc_attr( date( 'ga', $start ) );
				} else {
					$start_short = esc_attr( date( 'g.ia', $start ) );
				}
				if ( ! (int) date( 'i', $end ) ) {
					$end_short = esc_attr( date( 'ga', $end ) );
				} else {
					$end_short = esc_attr( date( 'g.ia', $end ) );
				}
				echo "<abbr title='$start_long'>$start_short</abbr> - <abbr title='$end_long'>$end_short</abbr>";
				break;
		}
	}

	/**
	 * Hooks the dynamic WP manage_participant_posts_columns filter to add a custom 
	 * column at the left edge of the table.
	 *
	 * @param array $cols An array of column information 
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function manage_participant_columns( $cols ) {
		$date = array_pop( $cols );
		$cols[ 'sessions' ] = 'Sessions';
		$cols[ 'participant-roles' ] = 'Roles';
		$cols[ 'date' ] = $date;
		return $cols;
	}

	/**
	 * Hooks the dynamic WP manage_participant_posts_custom_column filter to add content to
	 * our custom column.
	 *
	 * @param  
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function manage_participant_column_content( $col_name, $post_id ) {
		$terms = false;
		$post = get_post( $post_id );
		switch ( $col_name ) {
			case 'sessions' :
				$term = get_term_by( 'slug', $post->post_name, 'participants' );
				$sessions = get_objects_in_term( $term->term_id, 'participants' );
				if ( ! empty( $sessions ) ) {
					$out = array();
					foreach ( $sessions as $s ) {
						$out[] = sprintf( '<a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'action' => 'edit', 'post' => $s ), 'edit.php' ) ),
							esc_html( get_the_title( $s ) )
						);
					}
					echo join( ', ', $out );
				} else {
					_e( 'No sessions.', $this->name );
				}
				break;
			case 'participant-roles' :
				$terms = get_the_terms( $post_id, 'participant-roles' );
				$nothing = __( 'No roles', $this->name );
				$this->show_terms_cell( $terms, $nothing );
				break;
		}
	}
	
	/**
	 * Hooks the WP save_post action to:
	 * * Clear page caches when participants are saved
	 *
	 * @param int $post_id The post ID
	 * @param object $post The post object 
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function save_post( $post_id, $post ) {
		if ( $post->post_type == 'participant' ) {
			$this->update_participant( $post_id );
			$this->clear_page_caches();
		}
		if ( $post->post_type == 'session' ) {
			$this->process_schedule_metabox( $post_id );
			$this->clear_page_caches();
		}
	}

	/**
	 * Hooks the WP pre_insert_term filter to stop any term insertions
	 * in the synced taxonomies on any site except the index site.
	 *
	 * @param string $term The term to be added or updated
	 * @param string $taxonomy The taxonomy to which to add the term
	 * @return string The term to be added or updated
	 * @author Simon Wheatley
	 **/
	public function pre_insert_term( $term, $taxonomy ) {
		global $wpdb;
		
		// Allow terms to pass when we are syncing
		if ( $this->syncing_term )
			return $term;

		// Allow terms to pass if they aren't in a taxonomy we're syncing
		if ( $taxonomy != 'participants' )
			return $term;
		
		$this->no_editing_die( $taxonomy );
		exit;
	}
	
	
	// CALLBACKS
	// =========

	/**
	 * Callback function to provide HTML for the admin settings page.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function settings() {
		$vars = array();
		$vars[ 'options' ] = $this->get_all_options();
		$vars[ 'theme_copy' ] = false;
		$vars[ 'theme_manual_copy' ] = false;
		$vars[ 'theme_copied' ] = false;
		if ( $this->can_copy_theme() && ! $this->theme_present() ) {
			$vars[ 'theme_copy' ] = true;
			$vars[ 'theme_copy_button' ] = '<input type="submit" value="' . __( 'Copy', $this->name ) . '" class="button-secondary">';
		} elseif ( ! $this->can_copy_theme() && ! $this->theme_present() ) {
			$vars[ 'theme_manual_copy' ] = true;
		} elseif ( $this->theme_present() ) {
			$vars[ 'theme_copied' ] = true;
		}
		$this->render_admin( 'settings.php', $vars );
	}
	
	/**
	 * Callback function for the participants shortcode.
	 *
	 * @param  
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function shortcode_participants( $args = array(), $content = '' ) { 
		
		
		// This hook useful for temporarily removing actions/filters on template tags
		do_action( 'cs_pre_participants_shortcode' );
	
		$query_args = array(
			'ignore_sticky_posts' => true,
			'nopaging' => true, // Just get all the participants, recklessly...
			'order' => 'ASC',
			'orderby' => 'menu_order',
			'post_status' => 'publish', 
			'post_type' => 'participant',
					
		); 

		
		if ( $args[ 'role' ] ) {
			$query_args[ 'tax_query' ] = array(
				array( 
					'taxonomy' => 'participant-roles',
					'field' => 'slug',
					'terms' => $args[ 'role' ],
				),
			);
		}
		$vars = array();
		$vars[ 'participants' ] = new WP_Query( $query_args );
		
		$url = dirname(__FILE__);
		
		$output = $this->capture('shortcode-participants.php', $vars );
		// This hook useful for reinstating actions/filters on template tags
		do_action( 'cs_post_participants_shortcode' );
		return $output;
	}
	
	
	
	
	
	/**
	 * Callback function providing HTML for the Schedule metabox for sessions.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	public function schedule_metabox( $post, $metabox ) {
		$start = get_post_meta( $post->ID, '_cs_schedule_start', true );
		$end = get_post_meta( $post->ID, '_cs_schedule_end', true );
		$vars = array();
		if ( $start !== '' ) {
			$vars[ 'start_day' ] = mysql2date( 'U', date( 'Y-m-d ', $start ) . ' 00:00:00' );
			$vars[ 'start_hour' ] = date( 'H', $start );
			$vars[ 'start_minute' ] = date( 'i', $start );
		} else {
			$vars[ 'start_day' ] = false;
			$vars[ 'start_hour' ] = '-';
			$vars[ 'start_minute' ] = '-';
		}
		if ( $end !== '' ) {
			$vars[ 'end_day' ] = mysql2date( 'U', date( 'Y-m-d ', $end ) . ' 00:00:00' );
			$vars[ 'end_hour' ] = date( 'H', $end );
			$vars[ 'end_minute' ] = date( 'i', $end );
		} else {
			$vars[ 'end_day' ] = false;
			$vars[ 'end_hour' ] = '-';
			$vars[ 'end_minute' ] = '-';
		}
		$vars[ 'has_schedule' ] = get_post_meta( $post->ID, '_cs_has_schedule', true );
		$vars[ 'days' ] = $this->get_days_as_timestamps();
		$this->render_admin( 'metabox-schedule.php', $vars );
	}
	
	// UTILITIES
	// =========

	/**
	 * Provides HTML for a terms cell in an admin posts list table.
	 *
	 * @param array $terms An array of term objects to display 
	 * @param string $nothing The localised string to display if there's no terms
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function show_terms_cell( $terms, $nothing ) {
		if ( !empty( $terms ) ) {
			$post = get_post( $post_id );
			$out = array();
			foreach ( $terms as $c ) {
				$out[] = sprintf( '<a href="%s">%s</a>',
					esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'locations' => $c->slug ), 'edit.php' ) ),
					esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'terms', 'display' ) )
				);
			}
			echo join( ', ', $out );
		} else {
			echo $nothing;
		}
	}

	/**
	 * Updates a participant term when a participant post is updated.
	 *
	 * @param int $post_id The ID of the participant post 
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function update_participant( $post_id ) {
		$this->syncing_term = true;
		$post = get_post( $post_id );
		$term_id = (int) get_post_meta( $post_id, '_cs_term_id', true );

		$link = "<a href='" . get_edit_post_link( $post_id ) . "'>" . get_the_title( $post_id ) . "</a>";
		$args = array(
			'description' => sprintf( __( 'This term represents the participant %s.', $this->name ), $link ),
			'name' => get_the_title( $post_id ),
			'slug' => $post->post_name,
		);

		if ( $term_id && term_exists( $term_id, 'participants' ) && $post->post_status != 'publish' ) {
			wp_delete_term( $term_id, 'participants' );
			delete_post_meta( $post_id, '_cs_term_id' );
		} elseif ( $term_id && term_exists( $term_id, 'participants' ) ) {
			wp_update_term( $term_id, 'participants', $args );
		} elseif ( $post->post_status == 'publish' ) {
			$res = wp_insert_term( get_the_title( $post_id ), 'participants', $args );
			
			if(!is_wp_error($res))
				update_post_meta( $post_id, '_cs_term_id', $res[ 'term_id' ] );
		}

		$this->clear_page_caches();
		$this->syncing_term = false;
	}

	/**
	 * Processes the form fields for the schedule metabox, saves the results.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function process_schedule_metabox( $post_id ) {
		if ( ! @ $_POST[ '_cs_schedule_nonce' ] )
			return;
		check_admin_referer( 'cs_schedule', '_cs_schedule_nonce' );

		$has_schedule = (bool) @ $_POST[ 'cs_has_schedule' ];
		update_post_meta( $post_id, '_cs_has_schedule', $has_schedule );

		$start_day = @ $_POST[ 'cs_start_day' ];
		$start_hour = @ $_POST[ 'cs_start_hour' ];
		$start_minute = @ $_POST[ 'cs_start_minute' ];
		$end_day = @ $_POST[ 'cs_end_day' ];
		$end_hour = @ $_POST[ 'cs_end_hour' ];
		$end_minute = @ $_POST[ 'cs_end_minute' ];
		
		$start_datetime = date( 'Y-m-d ', $start_day ) . "$start_hour:$start_minute:00";
		update_post_meta( $post_id, '_cs_schedule_start', mysql2date( 'U', $start_datetime ) );

		$end_datetime = date( 'Y-m-d ', $end_day ) . "$end_hour:$end_minute:00";
		update_post_meta( $post_id, '_cs_schedule_end', mysql2date( 'U', $end_datetime ) );
		
	}

	/**
	 * Gets the days from the options as an array of timestamps.
	 *
	 * @return array An array of UNIX timestamps
	 * @author Simon Wheatley
	 **/
	protected function get_days_as_timestamps() {
		$days = explode( ',', $this->get_option( 'days' ) );
		foreach ( $days as & $day )
			$day = mysql2date( 'U', $day . ' 00:00:00' );
		return $days;
	}

	/**
	 * Clear the DB caches completely; currently only works with W3TC.
	 * 
	 * N.B. Doesn't clear MySQL Query Cache, just WP based DB caches.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function clear_db_caches() {
		if ( function_exists( 'w3tc_dbcache_flush' ) )
			w3tc_dbcache_flush();
	}

	/**
	 * Clear the DB caches completely; currently only works with W3TC.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function clear_minify_caches() {
		if ( function_exists( 'w3tc_minify_flush' ) )
			w3tc_minify_flush();
	}

	/**
	 * Clear the object caches completely; currently only works with W3TC.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function clear_object_caches() {
		wp_cache_flush();
		// Belt and braces, here we go…
		if ( function_exists( 'w3tc_objectcache_flush' ) )
			w3tc_objectcache_flush();
	}

	/**
	 * Clear the Page Cache completely; currently just works with W3TC.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function clear_page_caches() {
		if ( function_exists( 'w3tc_pgcache_flush' ) )
			w3tc_pgcache_flush();
	}
	
	/**
	 * Attempt to clear all caches; currently just works with W3TC.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function clear_all_caches() {
		$this->clear_db_caches();
		$this->clear_minify_caches();
		$this->clear_object_caches();
		$this->clear_page_caches();
	}
	
	/**
	 * Runs DB updates as required.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function update_db() {
		global $wpdb;
		$ver = $this->get_option( 'db_version', 0 );
		// return;
		if ( $ver < 1 ) {
			$sql = " UPDATE $wpdb->posts SET post_type = 'participant' WHERE post_type = 'speaker' ";
			$wpdb->query( $sql );
		}
		if ( $ver < 2 ) {
			$time_format = $this->get_option( 'time_format', 'g.ia' );
			$this->update_option( 'time_format', $time_format );
			$short_time_format = $this->get_option( 'short_time_format', 'ga' );
			$this->update_option( 'short_time_format', $short_time_format );
		}
	}
	
	/**
	 * Checks whether the WP theme directory is writable by PHP.
	 *
	 * @return boolean True if the theme root dir is writable
	 * @author Simon Wheatley
	 **/
	protected function can_copy_theme() {
		return is_writable( get_theme_root() );
	}
	
	/**
	 * Checks whether the theme directory 'twentyten-conference' 
	 * already exists.
	 *
	 * @return boolean True if the theme dir twentyten-conference exists
	 * @author Simon Wheatley
	 **/
	protected function theme_present() {
		return file_exists( get_theme_root() . '/twentyten-conference' );
	}
	
	/**
	 * Recursively copy files and dir structues from one dir 
	 * into another.
	 *
	 * @param string $src A filepath
	 * @param string $dst A filepath
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function recursive_copy( $src, $dst ) {
	    $dir = opendir( $src ); 
		error_log( "DST: $dst" );
	    mkdir( $dst );
		chmod( $dst, 0777 );
		while( false !== ( $file = readdir( $dir ) ) ) { 
			if ( ( $file != '.' ) && ( $file != '..' ) ) { 
				if ( is_dir( $src . '/' . $file ) ) { 
				$this->recursive_copy( $src . '/' . $file, $dst . '/' . $file ); 
				} else { 
					copy( $src . '/' . $file, $dst . '/' . $file );
					chmod( $dst . '/' . $file, 0777 );
				} 
			} 
		} 
	    closedir( $dir );	
	}
	
	/**
	 * Determines whether we are AJAX or regular HTML form submission, and 
	 * dies in an appropriate way with a helpful message.
	 *
	 * @return void
	 * @author Simon Wheatley
	 **/
	protected function no_editing_die() {
		$msg = sprintf( __( 'Please add or edit Participants <a href="%s">over here</a>.' ), admin_url( 'edit.php?post_type=participant' ) );
		if ( defined( 'DOING_AJAX' ) ) {
			$x = new WP_Ajax_Response();
			$x->add( array(
				'what' => 'taxonomy',
				'data' => new WP_Error('error', $msg )
			) );
			$x->send();
			exit; // Pure paranoia, the send method should end with die().
		}

		wp_die( $msg );
	}
	
	

} // END ConfSched class 

$conf_sched = new ConfSched(); 



?>