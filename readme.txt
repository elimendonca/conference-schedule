=== Conference Schedule ===
Contributors: simonwheatley
Donate link: http://www.simonwheatley.co.uk/wordpress/
Tags: conference, wordcamp, camp, barcamp, schedule, conference schedule
Requires at least: 3.1
Tested up to: 3.1
Stable tag: 0.52
 
Create a conference website, with speaker descriptions, session details and a schedule.

== Description ==

This plugin allows you to create a conference schedule in your WordPress website. You can create pages for speakers and sessions, and display an automatically created schedule of what's on.

The participant pages can be created with different roles, e.g. Speaker, Workshop Leader, etc, and each have an image with automatically created thumbnails. A shortcode (<code>[participants]</code>) allows you to list all your speakers, with links through to read more about them.

Sessions are created with start and end times, and allow you to specify a location and speaker. The main schedule and schedules for each location are automatically generated.

The plugin comes with a [Twenty Ten](http://wordpress.org/extend/themes/twentyten "The TwentyTen theme on the WordPress themes repository") child theme ([more on child themes](http://codex.wordpress.org/Child_Themes "The codex pages explaining WordPress child themes")), which you can use out of the box or as a reference to create your own conference theme.

This plugin handles describing and scheduling your conference and the key participants, it does not handle the ticketing.

= Template Tags =

Eventually I plan to abstract much of the raw PHP in the Conference Schedule theme templates into template tags. For now here's a few template tags as a statement of intent:

`<?php the_sessions( $before, $sep, $after ); ?>` - Very similar to the built-in template tag `[http://codex.wordpress.org/Function_Reference/the_tags](the_tags)`, used within a participant loop this will list the sessions the current participant is taking part in. Devs: note that there is a filter `cs_the_sessions` you can use on the output of this template tag.

`<?php get_the_sessions( $before, $sep, $after ); ?>` - Very similar to the built-in template tag `[http://codex.wordpress.org/Function_Reference/get_the_tags](get_the_tags)`, used within a participant loop this will return a list of the sessions the current participant is taking part in. Devs: note that there is a filter `cs_session_links` you can use on the output of this template tag.

`<?php has_schedule(); ?>` - When used on a Session post in a Session loop, this will tell you whether the current session has a schedule set.

`<?php the_start_time( $time_format, $short_time_format ); ?>` - When used on a Session post in a Session loop, this will show the scheduled session start time. The `time_format` and `short_time_format` strings are optional and are used to provide a date format, they default to the values on the Conference admin screen under the Settings section of the WordPress admin area. If the time is on the hour then the `short_time_format` is used, this enables you to output `10am` instead of `10:00am`, thus saving space. Devs: Note that there is a `cs_the_start_time` filter you can use on the output of this template tag.

`<?php get_the_start_time( $time_format, $short_time_format, $post ); ?>` - The same as the `the_start_time` template tag, except it returns the time rather than printing it. Devs: Note that there is a `cs_get_the_start_time` filter you can use on the output of this function.

`<?php the_end_time( $time_format, $short_time_format ); ?>` - When used on a Session post in a Session loop, this will show the scheduled end time for this session. Otherwise identical to `the_start_time` above. Devs: Note that there is a `cs_the_start_time` filter you can use on the output of this template tag.

`<?php get_the_end_time( $time_format, $short_time_format, $post ); ?>` - The same as the `the_end_time` template tag, except it returns the time rather than printing it. Devs: Note that there is a `cs_get_the_end_time` filter you can use on the output of this function.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in the WordPress admin area
3. Go to 'Conference' under 'Settings' in the WordPress admin area and set the days for your conference
4. If you're using the TwentyTen theme, use the copy functionality to load the conference theme into place

== Screenshots ==

1. **Participants** List your participants, either all of them or just specific participant roles, e.g. all Workshop Leaders.
2. **Schedule** The main schedule and location specific schedules are automatically generated for you.
3. **Editing Participants** See your participants at a glance, and edit their roles and descriptions.
4. **Editing Sessions** Look over your sessions and change the scheduling, participants and locations.

== Changelog ==

= 0.52 =

* Added and documented the `has_schedule`, `the_start_time`, `get_the_start_time`, `the_end_time`, and `get_the_end_time` template tags.
* hCal microformat in the theme.
* Added POT file for translators.

= 0.51 =

* Added and documented the `the_sessions` and `get_the_sessions` template tags.

= 0.5 =
* First release!
