<?php

// === Show Content Template Adam ===
// Package: radio-station
// Author: Tony Hayes
// @since 2.3.0

// -----------------
// Set Template Data
// -----------------

// --- get global and get show post ID ---
global $radio_station_data, $post;
$post_id = $radio_station_data['show-id'] = $post->ID;

// --- get schedule time format ---
$time_format = (int) radio_station_get_setting( 'clock_time_format', $post_id );

// --- get show meta data ---
$show_title = get_the_title( $post_id );
$header_id = get_post_meta( $post_id, 'show_header', true );
$avatar_id = get_post_meta( $post_id, 'show_avatar', true );
$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
$genres = wp_get_post_terms( $post_id, RADIO_STATION_GENRES_SLUG );
$languages = wp_get_post_terms( $post_id, RADIO_STATION_LANGUAGES_SLUG );
$hosts = get_post_meta( $post_id, 'show_user_list', true );
$producers = get_post_meta( $post_id, 'show_producer_list', true );
$active = get_post_meta( $post_id, 'show_active', true );
$shifts = radio_station_get_show_schedule( $post_id );
$podcast = get_post_meta( $post_id, 'podcast', true );
$podcast_rss = get_post_meta( $post_id, 'podcast_rss', true );
$podcast_embed = get_post_meta( $post_id, 'podcast_embed', true );

// --- get show icon / button data ---
$show_file = get_post_meta( $post_id, 'show_file', true );
$show_link = get_post_meta( $post_id, 'show_link', true );
$show_email = get_post_meta( $post_id, 'show_email', true );
$show_patreon = get_post_meta( $post_id, 'show_patreon', true );
$patreon_title = __( 'Become a Supporter for', 'radio-station' ) . ' ' . $show_title;
// $show_rss = get_post_meta( $post_id, 'show_rss', true );
$show_rss = false; // TEMP

// 2.3.2: added show download disabled check
$show_download = true;
$download = get_post_meta( $post_id, 'show_download', true );
if ( 'on' == $download ) {
	// --- on = disabled ---
	$show_download = false;
}

// --- filter all show meta data ---
// 2.3.2: added show download filter
$show_title = apply_filters( 'radio_station_show_title', $show_title, $post_id );
$header_id = apply_filters( 'radio_station_show_header', $header_id, $post_id );
$avatar_id = apply_filters( 'radio_station_show_avatar', $avatar_id, $post_id );
$thumbnail_id = apply_filters( 'radio_station_show_thumbnail', $thumbnail_id, $post_id );
$genres = apply_filters( 'radio_station_show_genres', $genres, $post_id );
$languages = apply_filters( 'radio_station_show_languages', $languages, $post_id );
$hosts = apply_filters( 'radio_station_show_hosts', $hosts, $post_id );
$producers = apply_filters( 'radio_station_show_producers', $producers, $post_id );
$active = apply_filters( 'radio_station_show_active', $active, $post_id );
$shifts = apply_filters( 'radio_station_show_shifts', $shifts, $post_id );
$show_file = apply_filters( 'radio_station_show_file', $show_file, $post_id );
$show_download = apply_filters( 'radio_station_show_download', $show_download, $post_id );
$show_link = apply_filters( 'radio_station_show_link', $show_link, $post_id );
$show_email = apply_filters( 'radio_station_show_email', $show_email, $post_id );
$show_patreon = apply_filters( 'radio_station_show_patreon', $show_patreon, $post_id );
$patreon_title = apply_filters( 'radio_station_show_patreon_title', $patreon_title, $post_id );
$show_rss = apply_filters( 'radio_station_show_rss', $show_rss, $post_id );

// --- create show icon display early ---
// 2.3.0: converted show links to icons
$show_icons = array();

// --- show home link icon ---
if ( $show_link ) {
	$title = esc_attr( __( 'Show Website', 'radio-station' ) );
	$icon = '<span style="color:#A44B73;" class="dashicons dashicons-admin-links"></span>';
	$icon = apply_filters( 'radio_station_show_home_icon', $icon, $post_id );
	$show_icons['home'] = '<div class="show-icon show-website">';
	$show_icons['home'] .= '<a href="' . esc_url( $show_link ) . '" title="' . $title . '" target="_blank">';
	$show_icons['home'] .= $icon;
	$show_icons['home'] .= '</a>';
	$show_icons['home'] .= '</div>';
}

// --- email DJ / host icon ---
if ( $show_email ) {
	$title = esc_attr( __( 'Email Show Host', 'radio-station' ) );
	$icon = '<span style="color:#0086CC;" class="dashicons dashicons-email"></span>';
	$icon = apply_filters( 'radio_station_show_email_icon', $icon, $post_id );
	$show_icons['email'] = '<div class="show-icon show-email">';
	$show_icons['email'] .= '<a href="mailto:' . sanitize_email( $show_email ) . '" title="' . $title . '">';
	$show_icons['email'] .= $icon;
	$show_icons['email'] .= '</a>';
	$show_icons['email'] .= '</div>';
}

// --- show RSS feed icon ---
if ( $show_rss ) {
	$feed_url = radio_station_get_show_rss_url( $post_id );
	$title = esc_attr( __( 'Show RSS Feed', 'radio-station' ) );
	$icon = '<span style="color:#FF6E01;" class="dashicons dashicons-rss"></span>';
	$icon = apply_filters( 'radio_station_show_rss_icon', $icon, $post_id );
	$show_icons['rss'] = '<div class="show-icon show-rss">';
	$show_icons['rss'] .= '<a href="' . esc_url( $feed_url ) . '" title="' . $title . '">';
	$show_icons['rss'] .= $icon;
	$show_icons['rss'] .= '</a>';
	$show_icons['rss'] .= '</div>';
}

// --- filter show icons ---
$show_icons = apply_filters( 'radio_station_show_page_icons', $show_icons, $post_id );

// --- set show related defaults ---
$show_latest = $show_posts = $show_playlists = $show_episodes = false;

// --- check for latest show blog posts ---
// $latest_limit = radio_station_get_setting( 'show_latest_posts' );
// $latest_limit = false;
// $latest_limit = apply_filters( 'radio_station_show_page_latest_limit', $latest_limit, $post_id );
// if ( absint( $latest_limit ) > 0 ) {
//	$show_latest = radio_station_get_show_posts( $post_id, array( 'limit' => $latest_limit ) );
// }

// --- check for show blog posts ---
$posts_per_page = radio_station_get_setting( 'show_posts_per_page' );
if ( absint( $posts_per_page ) > 0 ) {
	$limit = apply_filters( 'radio_station_show_page_posts_limit', false, $post_id );
	$show_posts = radio_station_get_show_posts( $post_id, array( 'limit' => $limit ) );
}

// --- check for show playlists ---
$playlists_per_page = radio_station_get_setting( 'show_playlists_per_page' );
if ( absint( $playlists_per_page ) > 0 ) {
	$limit = apply_filters( 'radio_station_show_page_playlist_limit', false, $post_id );
	$show_playlists = radio_station_get_show_playlists( $post_id, array( 'limit' => $limit ) );
}

// --- check for show episodes ---
$episodes_per_page = radio_station_get_setting( 'show_episodes_per_page' );
$show_episodes = apply_filters( 'radio_station_show_page_episodes', false, $post_id );

// --- get layout display settings ----
$block_position = radio_station_get_setting( 'show_block_position' );
$section_layout = radio_station_get_setting( 'show_section_layout' );
$jump_links = apply_filters( 'radio_station_show_jump_links', 'yes', $post_id );


// ------------------
// Set Blocks Content
// ------------------

// --- set empty blocks ---
$blocks = array( 'show_images' => '', 'show_meta' => '', 'show_schedule' => '' );

// Show Images Block
// -----------------
if ( ( $avatar_id || $thumbnail_id ) || ( count( $show_icons ) > 0 ) || ( $show_file ) ) {

	// --- Show Avatar ---
	if ( $avatar_id || $thumbnail_id ) {
		// --- get show avatar (with thumbnail fallback) ---
		$size = apply_filters( 'radio_station_show_avatar_size', 'medium', $post_id, 'show-page' );
		$attr = array( 'class' => 'show-image' );
		if ( $show_title ) {
			$attr['alt'] = $attr['title'] = $show_title;
		}
		$show_avatar = radio_station_get_show_avatar( $post_id, $size, $attr );
		if ( $show_avatar ) {
			if ( $header_id ) {
				$class = ' has-header-image';
			} else {
				$class = '';
			}
			$blocks['show_images'] .= '<div class="show-avatar' . esc_attr( $class ) . '">';
			$blocks['show_images'] .= $show_avatar;
			$blocks['show_images'] .= '</div>';
		}
	}

	// --- show controls ---
	if ( ( count( $show_icons ) > 0 ) || ( $show_file ) ) {

		$blocks['show_images'] .= '<div class="show-controls">';

		// --- Show Icons ---
		if ( count( $show_icons ) > 0 ) {
			$blocks['show_images'] .= '<div class="show-icons">';
			$blocks['show_images'] .= implode( "\n", $show_icons );
			$blocks['show_images'] .= '</div>';
		}

		// --- Show Patreon Button ---
		$show_patreon_button = '';
		if ( $show_patreon ) {
			$show_patreon_button .= '<div class="show-patreon">';
			$show_patreon_button .= radio_station_patreon_button( $show_patreon, $patreon_title );
			$show_patreon_button .= '</div>';
		}
		// 2.3.1: added filter for patreon button
		$show_patreon_button = apply_filters( 'radio_station_show_patreon_button', $show_patreon_button, $post_id );
		$blocks['show_images'] .= $show_patreon_button;

		// --- Show Player ---
		// 2.3.0: embed latest broadcast audio player
		if ( $show_file ) {
		
			$blocks['show_images'] .= '<div class="show-player">';
			$shortcode = '[audio src="' . $show_file . '" preload="metadata"]';
			$player_embed = do_shortcode( $shortcode );
			$blocks['show_images'] .= '<div class="show-embed">';
			$blocks['show_images'] .= $player_embed;
			$blocks['show_images'] .= '</div>';

			// --- Download Audio Icon ---
			// 2.3.2: check show download switch
			if ( $show_download ) {
				$title = __( 'Download Latest Broadcast', 'radio-station' );
				$blocks['show_images'] .= '<div class="show-download">';
				$blocks['show_images'] .= '<a href="' . esc_url( $show_file ) . '" title="' . esc_attr( $title ) . '">';
				$blocks['show_images'] .= '<span style="color:#7DBB00;" class="dashicons dashicons-download"></span>';
				$blocks['show_images'] .= '</a>';
				$blocks['show_images'] .= '</div>';
			}
			
			$blocks['show_images'] .= '</div>';
		}

		$blocks['show_images'] .= '</div>';
	}
}

// Show Meta Block
// ---------------
if ( $hosts || $producers || $genres || $languages ) {

	// --- show meta title ---
	$blocks['show_meta'] = '<h4>' . esc_html( __( 'Show Info', 'radio-station' ) ) . '</h4>';

	// --- Show DJs / Hosts ---
	if ( $hosts ) {
		$blocks['show_meta'] .= '<div class="show-djs show-hosts">';
		$blocks['show_meta'] .= '<b>' . esc_html( __( 'Hosted by', 'radio-station' ) ) . '</b>: ';
		$count = 0;
		$host_count = count( $hosts );
		foreach ( $hosts as $host ) {
			$count ++;
			$user_info = get_userdata( $host );

			// --- DJ / Host URL / display---
			$host_url = radio_station_get_host_url( $host );
			if ( $host_url ) {
				$blocks['show_meta'] .= '<a href="' . esc_url( $host_url ) . '">';
			}
			$blocks['show_meta'] .= esc_html( $user_info->display_name );
			if ( $host_url ) {
				$blocks['show_meta'] .= '</a>';
			}

			if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
			     || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
				$blocks['show_meta'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
			} elseif ( ( count( $hosts ) > 2 ) && ( $count < count( $hosts ) ) ) {
				$blocks['show_meta'] .= ', ';
			}
		}
		$blocks['show_meta'] .= '</div>';
	}

	// --- Show Producers ---
	// 2.3.0: added assigned producer display
	if ( $producers ) {
		$blocks['show_meta'] .= '<div class="show-producers">';
		$blocks['show_meta'] .= '<b>' . esc_html( __( 'Produced by', 'radio-station' ) ) . '</b>: ';
		$count = 0;
		$producer_count = count( $producers );
		foreach ( $producers as $producer ) {
			$count ++;
			$user_info = get_userdata( $producer );

			// --- Producer URL / display ---
			$producer_url = radio_station_get_producer_url( $producer );
			if ( $producer_url ) {
				$blocks['show_meta'] .= '<a href="' . esc_url( $producer_url ) . '">';
			}
			$blocks['show_meta'] .= esc_html( $user_info->display_name );
			if ( $producer_url ) {
				$blocks['show_meta'] .= '</a>';
			}

			if ( ( ( 1 === $count ) && ( 2 === $producer_count ) )
			     || ( ( $producer_count > 2 ) && ( ( $producer_count - 1 ) === $count ) ) ) {
				$blocks['show_meta'] .= ' ' . esc_html( __( 'and', 'radio-station' ) ) . ' ';
			} elseif ( ( count( $producers ) > 2 ) && ( $count < count( $producers ) ) ) {
				$blocks['show_meta'] .= ', ';
			}
		}
		$blocks['show_meta'] .= '</div>';
	}

	// --- Show Genre(s) ---
	// 2.3.0: only display if genre assigned
	if ( $genres ) {
		$genre_count = count( $genres );
		$tax_object = get_taxonomy( RADIO_STATION_GENRES_SLUG );
		if ( count( $genres ) == 1 ) {
			$label = $tax_object->labels->singular_name;
		} else {
			$label = $tax_object->labels->name;
		}
		$blocks['show_meta'] .= '<div class="show-genres">';
		$blocks['show_meta'] .= '<b>' . esc_html( $label ) . '</b>: ';
		$genre_links = array();
		foreach ( $genres as $genre ) {
			$genre_link = get_term_link( $genre );
			$genre_links[] = '<a href="' . esc_url( $genre_link ) . '">' . esc_html( $genre->name ) . '</a>';
		}
		$blocks['show_meta'] .= implode( ', ', $genre_links );
		$blocks['show_meta'] .= '</div>';
	}

	// --- Show Language(s) ---
	// 2.3.0: only display if language is assigned
	if ( $languages ) {
		$tax_object = get_taxonomy( RADIO_STATION_LANGUAGES_SLUG );
		if ( count( $languages ) == 1 ) {
			$label = $tax_object->labels->singular_name;
		} else {
			$label = $tax_object->labels->name;
		}

		$blocks['show_meta'] .= '<div class="show-languages">';
		$blocks['show_meta'] .= '<b>' . esc_html( $label ) . '</b>: ';
		$language_links = array();
		foreach ( $languages as $language ) {
			$lang_label = $language->name;
			if ( !empty( $language->description ) ) {
				$lang_label .= ' (' . $language->description . ')';
			}
			$language_link = get_term_link( $language );
			$language_links[] = '<a href="' . esc_url( $language_link ) . '">' . esc_html( $lang_label ) . '</a>';
		}
		$blocks['show_meta'] .= implode( ', ', $language_links );
		$blocks['show_meta'] .= '</div>';
	}
}

// Show Times Block
// ----------------

// --- check to remove incomplete and disabled shifts ---
if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
	foreach ( $shifts as $i => $shift ) {
		$shift = radio_station_validate_shift( $shift );
		if ( isset( $shift['disabled'] ) && ( 'yes' == $shift['disabled'] ) ) {
			unset( $shifts[$i] );
		}
	}
	if ( count( $shifts ) == 0 ) {
		$shifts = false;
	}
}

// --- show times title ---
$show_times_box = '';

// --- check if show is active and has shifts ---
if ( !$active || !$shifts ) {

	$show_times_box .= esc_html( __( 'Not Currently Scheduled.', 'radio-station' ) );

} else {

	// --- get timezone and offset ---
	// 2.3.2: use get timezone function
	$timezone = radio_station_get_timezone();
	if ( strstr( $timezone, 'UTC' ) ) {
		$offset = str_replace( 'UTC', '', $timezone );
	} else {
		$timezone_code = radio_station_get_timezone_code( $timezone );
		$datetimezone = new DateTimeZone( $timezone );
		$offset = $datetimezone->getOffset( new DateTime() );
		$offset = round( $offset / 60 / 60 );

		if ( strstr( (string) $offset, '.' ) ) {
			if ( substr( $offset, - 2, 2 ) == '.5' ) {
				$offset = str_replace( '.5', ':30', $offset );
			} elseif ( substr( $offset, - 3, 3 ) == '.75' ) {
				$offset = str_replace( '.75', ':45', $offset );
			} elseif ( substr( $offset, - 3, 3 ) == '.25' ) {
				$offset = str_replace( '.25', ':15', $offset );
			}
		}
	}
	
	if ( ( 0 == $offset ) || ( '' == $offset ) ) {
		$utc_offset = '';
	} elseif ( $offset > 0 ) {
		$utc_offset = '+' . $offset;
	} else {
		$utc_offset = $offset;
	}	
	
	// TODO: --- display user timezone ---
	// $block['show_times'] .= ...
	
	//Count days of the week
	$show_days_count = 0;

	$show_times_box .= '<div class="show-times">' . "\n";
	$found_encore = false;

	// --- get data format ---
	// 2.3.2: set filterable time formats
	if ( 24 == (int) $time_format ) {
		$start_data_format = $end_data_format = 'H:i';
	} else {
		$start_data_format = $end_data_format = 'g:i a';
	}
	$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'show-template', $post_id );
	$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'show-template', $post_id );

	$weekdays = radio_station_get_schedule_weekdays();
	$now = radio_station_get_now();
	
	foreach ( $weekdays as $day ) {
		$show_times = array();
		if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
			foreach ( $shifts as $shift ) {
				if ( $day == $shift['day'] ) {

					// --- convert shift info ---
					// 2.3.2: replace strtotime with to_time for timezones
					// 2.3.2: fix to convert to 24 hour format first
					$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
					$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
					$start_time = radio_station_convert_shift_time( $start );
					$end_time = radio_station_convert_shift_time( $end );
					$shift_start_time = radio_station_to_time( $start_time );
					$shift_end_time = radio_station_to_time( $end_time );
					if ( $shift_end_time < $shift_start_time ) {
						$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
					}

					// --- get shift time display ---
					$start = radio_station_get_time( $start_data_format, $shift_start_time );
					$end = radio_station_get_time( $end_data_format, $shift_end_time );
					$start = radio_station_translate_time( $start );
					$end = radio_station_translate_time( $end );				

					// --- check if current shift ---
					$classes = array( 'show-day-time' );
					$class = implode( ' ', $classes );

					// --- set show time output ---
					$show_time = '<span class="' . esc_attr( $class ) . '">' . "\n";
					$show_time .= '<span class="rs-time rs-start-time" data-shift-start="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
					$show_time .= '<span class="rs-sep"> - </span>' . "\n";
					$show_time .= '<span class="rs-time rs-end-time" data-shift-end="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";
					
					if ( isset( $shift['encore'] ) && ( 'on' == $shift['encore'] ) ) {
						$found_encore = true;
						$show_time .= '<span class="show-encore">*</span>' . "\n";
					}
					$show_time .= '</span>' . "\n";
					$show_times[] = $show_time;
					$show_days_count++;
				}
			}
		}
		//var_dump($show_days_count);
		$show_times_count = count( $show_times );
		if ( $show_times_count > 0 ) {
			$show_times_box .= '<span class="show-day ' . strtolower( $day ) . '">';
			$weekday = radio_station_translate_weekday( $day, true );
			$show_times_box .= esc_html( $weekday ) . '@ ';
			$show_times_box .= '</span>' . "\n";
			foreach ( $show_times as $i => $show_time ) {
				$show_times_box .=  $show_time . '<br>' . "\n";
				// if ( $i < ( $show_times_count - 1 ) ) {
				//	$show_times_box .= '<br>';
				// }
			}
		}
	}

	// --- * encore note ---
	if ( $found_encore ) {
		$show_times_box .= '<div>';
		$show_times_box .= '<span class="show-encore">*</span> ';
		$show_times_box .= '<span class="show-encore-label">';
		$show_times_box .= esc_html( __( 'Encore Presentation', 'radio-station' ) );
		$show_times_box .= '</span>';
		$show_times_box .= '</div>';
	}

	$show_times_box .= '</div>' . "\n";
}

// --- filter show info blocks ---
$blocks = apply_filters( 'radio_station_show_page_blocks', $blocks, $post_id );


// -----------------
// Set Show Sections
// -----------------
// 2.3.0: add show information sections

// Set Show Description
// --------------------
$show_description = false;
if ( strlen( trim( $content ) ) > 0 ) {
	$show_description = '<div class="show-desc">' . $content . '</div>';
}

// Show Sections
// -------------
$sections = array();
if ( ( strlen( trim( $content ) ) > 0 ) || $show_posts || $show_playlists || $show_episodes ) {

	// --- About Show Tab (Post Content) ---
	$i = 0;
	
	// --- Show Episodes Tab ---
	if ( $show_episodes ) {

		$sections['episodes']['heading'] = '<a name="show-episodes"></a>';
		$sections['episodes']['heading'] .= '<h3 id="show-section-episodes">' . esc_html( __( 'Show Episodes', 'radio-station' ) ) . '</h3>';
		$sections['episodes']['anchor'] = __( 'Episodes', 'radio-station' );

		$sections['episodes']['content'] = '<div id="show-episodes" class="show-section-content"><br>';
		$radio_station_data['show-episodes'] = $show_posts;
		$shortcode = '[show-episodes-archive per_page="' . $episodes_per_page . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_episodes_shortcode', $shortcode, $post_id );
		$sections['episodes']['content'] .= do_shortcode( $shortcode );
		$sections['episodes']['content'] .= '</div>';
		$i ++;
	}

	// --- Show Blog Posts Tab ---
	if ( $show_posts ) {

		$sections['posts']['heading'] = '<a name="show-posts"></a>';
		$sections['posts']['heading'] .= '<h3 id="show-section-posts">' . esc_html( __( 'Show Posts', 'radio-station' ) ) . '</h3>';
		$sections['posts']['anchor'] = __( 'Posts', 'radio-station' );

		$sections['posts']['content'] = '<div id="show-posts" class="show-section-content"><br>';
		$radio_station_data['show-posts'] = $show_posts;
		$shortcode = '[show-posts-archive per_page="' . $posts_per_page . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_posts_shortcode', $shortcode, $post_id );
		$sections['posts']['content'] .= do_shortcode( $shortcode );
		$sections['posts']['content'] .= '</div>';
		$i ++;
	}

	// --- Show Playlists Tab ---
	if ( $show_playlists ) {

		$sections['playlists']['heading'] = '<a name="show-playlists">';
		$sections['playlists']['heading'] .= '<h3 id="show-section-playlists">' . esc_html( __( 'Show Playlists', 'radio-station' ) ) . '</h3>';
		$sections['playlists']['anchor'] = __( 'Playlists', 'radio-station' );

		$sections['playlists']['content'] = '<div id="show-playlists" class="show-section-content"><br>';
		$radio_station_data['show-playlists'] = $show_playlists;
		$shortcode = '[show-playlists-archive per_page="' . $playlists_per_page . '"]';
		$shortcode = apply_filters( 'radio_station_show_page_playlists_shortcode', $shortcode, $post_id );
		$sections['playlists']['content'] .= do_shortcode( $shortcode );
		$sections['playlists']['content'] .= '</div>';
		$i ++;
	}
}
$sections = apply_filters( 'radio_station_show_page_sections', $sections, $post_id );


// --- Genres ---
if ( $genres ) {
	$tax_object = get_taxonomy( RADIO_STATION_GENRES_SLUG );
	if ( count( $genres ) == 1 ) {
		$label = $tax_object->labels->singular_name;
	} else {
		$label = $tax_object->labels->name;
	}
	$genre_box .= '<div class="show-genres">'. "\n";
	$genre_links = array();
	foreach ( $genres as $genre ) {
		//$genre_link = get_term_link( $genre );
		//$genre_links[] = '<a href="' . esc_url( $genre_link ) . '">' . esc_html( $genre->name ) . '</a>' . "\n"
		$genre_links[] = esc_html( $genre->name ) . "\n";
	}
	$genre_box .= implode( '<br><br>', $genre_links );
	$genre_box .= '</div>' . "\n";
}

// ---------------
// Template Output
// ---------------

?>
	
	<!-- #show-content -->
	<div id="show-content">
			
		<?php
		// --- If there is show description and show times, show full row ---
		if ( $show_description && $show_days_count )
		{ 
			//Row Start
			$string = '<div class="row">' . "\n";
			
			//About
			$string .= '<div class="col-md-8 col-xs-12">' . "\n";
			$string .='<h3>About</h3>' . "\n";
			$string .= $show_description;
			$string .= '</div>' . "\n";
			
			//Show Times
			$string .= '<div class="col-md-4 col-xs-12">' . "\n";
			if($show_days_count == 1)
			{
				$string .='<h3>Show Time</h3>' . "\n";
			}
			else
			{
				$string .='<h3>Show Times</h3>' . "\n";
			}
			$string .= $show_times_box;
			
			//Genres
			$string .= '<div class="mt-md-5">' . "\n";
			if( count( $genres ) == 1)
			{
				$string .='<h3>Genre</h3>' . "\n";
			}
			elseif( count( $genres ) > 1)
			{
				$string .='<h3>Genres</h3>' . "\n";
			}
			$string .= $genre_box;

			$string .= '</div>' . "\n";
			$string .= '</div>' . "\n";
			//Row End
			$string .= '</div>' . "\n";
			echo $string;
		}		
		// --- If there is show description and genres, show full row ---
		else if ( $show_description && $genres )
		{ 
			//Row Start
			$string = '<div class="row">' . "\n";
			//About
			$string .= '<div class="col-md-8 col-xs-12">' . "\n";
			$string .='<h3>About</h3>' . "\n";
			$string .= $show_description;
			$string .= '</div>' . "\n";

			//Genres
			$string .= '<div class="col-md-4 col-xs-12">' . "\n";
			if( count( $genres ) == 1)
			{
				$string .='<h3>Genre</h3>' . "\n";
			}
			elseif( count( $genres ) > 1)
			{
				$string .='<h3>Genres</h3>' . "\n";
			}
			$string .= $genre_box;	
			$string .= '</div>' . "\n";
			
			//Row End
			$string .= '</div>' . "\n";
			echo $string;
		}
		// --- If there is only show description, show as full row ---
		elseif ( $show_description )
		{
			$string = '<div class="row">' . "\n";
			//About
			$string .= '<div class="col-md-12">' . "\n";
			$string .='<h3>About</h3>' . "\n";
			$string .= $show_description;
			$string .= '</div>' . "\n";
			$string .= '</div>' . "\n";
			echo $string;
		}

		// --- If there is hosts, show them ---
		if ( $hosts )
		{ 
			$host_box = '<div class="row">' . "\n";
			$host_box .= '<div class="col-md-12">' . "\n";
			if( count( $hosts ) == 1)
			{
				$host_box .= '<h3>Host</h3>' . "\n";
			}
			elseif( count( $hosts ) > 1)
			{
				$host_box .= '<h3>Hosts</h3>' . "\n";
			}
			$host_box .= '</div>' . "\n";
			$host_box .= '</div>' . "\n";
			//$host_box .= '<div class="show-djs show-hosts">';
			$count = 0;
			$none = 0;
			$host_count = count( $hosts );
			foreach ( $hosts as $host ) {
				$user_info = get_userdata( $host );
				$user_name = $user_info->display_name;
				$user_ID = $user_info->ID;
				//$avatar = get_avatar_url($user_ID);
				//$avatar = str_replace("-150x150","", $avatar);
				$avatar = get_wpupa_url($user_ID, ['size' => 'medium']);
				//$user_avatar = '<div class="img-fluid">' . get_avatar($user_ID, '500', $default, $alt, array( 'class' => array( 'd-block', 'mx-auto' ) ))  . '</div>' . "\n";
				$user_avatar = '<div class="show-host-avatar"><img class="img-fluid" alt="' . $user_name . "'s Picture" . '" src="' . $avatar . '"></div>' . "\n";	
				//$user_avatar = '<img class="img-fluid" src="' . get_avatar_url($user_ID)  . '">' . "\n";
				$user_meta = get_user_meta( $host );
				$user_desc = $user_meta['description'][0]; 		
				//var_dump($user_info->ID);
				//$host_box .= esc_html( $user_name );
				
				//Check if a user description is set, if it isn't then start counting how many aren't set
				if($user_desc === '')
				{
					$none++;
				}
				//If the descriptions of the people that have none is greater than or equal to the count of hosts
				//Make sure none is not 0 to make sure it's not false
				//If both are true, just show the name and avatar of the host
				if($none <= $host_count && $none != 0)
				{
					if($count == 0) { $host_box .= '<div class="row">' . "\n"; }
					$host_box .= "<div class='col-md-4 col-xs-12'>" . "\n";
					$host_box .= "<div class='user-name'>" . $user_name . "</div>" . "\n";
					$host_box .= $user_avatar;
					$host_box .= '</div>' . "\n";
					if( !next( $hosts )) { $host_box .= '</div>' . "\n"; }
				}
				//Show Everything
				else
				{
					$host_box .= "<div class='user-name'>" . $user_name . "</div>" . "\n";
					$host_box .= '<div class="row">' . "\n";
					$host_box .= "\n" . "<div class='col-md-4 col-xs-12'>" . "\n";
					$host_box .= $user_avatar;
					$host_box .= '</div>' . "\n";
					
					$host_box .= "\n" . "<div class='col-md-8 col-xs-12'>" . "\n";
					$host_box .= "<div class='user-description'>" . $user_desc . "</div>" . "<br> \n";
					$host_box .= '</div>' . "\n";
					$host_box .= '</div>' . "\n";
					//$host_box .= '<br><br>'  . "\n";
				}
				$count++;
			}
			echo $host_box;	
		}

		// --- If podcast is set to true allow a row to be created with a RSS or Embed Feed ---
		if ( $podcast == "true" )
		{ 
			$podcast_content = '<div class="row">' . "\n";	
			//If it's RSS, take apart the XML and create boxes
			if ( $podcast_rss )
			{
				//Basics
				$count = 0;
				$feeds = simplexml_load_file($podcast_rss);
				$items = $feeds->channel->item;

				//Episodes Loop
				foreach ($items as $item) 
				{
					$title = $item->title;
					$description = html_entity_decode(strip_tags($item->description));	
					$link = $item->link;
					$icon = '<i class="my-auto mx-auto far fa-play-circle fa-3x"></i>';
					if(strpos($description, ".") !== false) { $preview = substr($description, 0, strpos($description, ".")); }
					else { $preview = $description; }
					//echo(html_entity_decode($item->pubDate));
					
					
					//<a href="https://www.w3schools.com/">Visit W3Schools.com!</a>
					
					//Row Start
					$rss_string .= '<div class="row podcast border">' . "\n";	
					
					//Play Icon
					$rss_string .= '<div class="col-md-2 podcast-image d-flex align-items-stretch">' . "\n";
					$rss_string .= '<a class="my-auto mx-auto"  href="' . $link . '" target="_blank">' . $icon . '</a>';
					$rss_string .= '</div>' . "\n";					
					
					//Episode
					$rss_string .= '<div class="col-md-10 podcast-text d-flex align-items-stretch flex-fill text-white dark-blue">' . "\n";		
					$rss_string .= '<div class="podcast-episode">' . "\n";
					$rss_string .= '<h4>' . $title . '</h4>' . "\n";	
					$rss_string .= '<div>'. $preview . '</div>';	
					$rss_string .= '</div>' . "\n";		
					$rss_string .= '</div>' . "\n";	
					
					//Row End
					$rss_string .= '</div>' . "\n";					
					
					$count++;
					if ($count == 4)
					{
						break;
					}
				}
				
				//$podcast_content .= do_shortcode($podcast_rss);
				$podcast_content .= '<div class="col-md-12">' . "\n";
				$podcast_content .= '<h3>Episodes</h3>' . "\n";	
				$podcast_content .= '</div>' . "\n";
				$podcast_content .= '</div>' . "\n";
				$podcast_content .= '<div class="container">' . "\n";
				$podcast_content .= $rss_string;
				$podcast_content .= '</div>' . "\n";
			}
			else if ( $podcast_embed )
			{
				$podcast_content .= '<div class="col-md-12">';
				$podcast_content .= '<h3>Episodes</h3>' . "\n";	
				$podcast_content .= $podcast_embed;
				$podcast_content .= '</div>';
			}
			$podcast_content .= '</div>';
			echo $podcast_content ;			
		}
		?>
    </div>
    <!-- /#show-content -->

<?php

// --- enqueue show page script ---
// 2.3.0: enqueue script instead of echoing
radio_station_enqueue_script( 'radio-station-show-page', array( 'radio-station' ), true );