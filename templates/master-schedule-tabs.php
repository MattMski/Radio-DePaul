<?php
/**
 * Template for master schedule shortcode tabs style.
 * ref: http://nlb-creations.com/2014/06/06/radio-station-tutorial-creating-a-tabbed-programming-schedule/
 */

// --- get all the required info ---
$schedule = radio_station_get_current_schedule();
$hours = radio_station_get_hours();
$now = radio_station_get_now();
$date = radio_station_get_time( 'date', $now );
$today =  radio_station_get_time( 'day', $now );
// $am = str_replace( ' ', '', radio_station_translate_meridiem( 'am' ) );
// $pm = str_replace( ' ', '', radio_station_translate_meridiem( 'pm' ) );

// --- set shift time formats ---
// 2.3.2: set time formats early
if ( 24 == (int) $atts['time'] ) {
	$start_data_format = $end_data_format = 'H:i';
} else {
	$start_data_format = $end_data_format = 'g:i a';
}
$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format, 'schedule-tabs', $atts );
$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format, 'schedule-tabs', $atts );

// --- get schedule days and dates ---
// 2.3.2: allow for start day attibute
if ( isset( $atts['start_day'] ) && $atts['start_day'] ) {
	$weekdays = radio_station_get_schedule_weekdays( $atts['start_day'] );
} else {
	$weekdays = radio_station_get_schedule_weekdays();
}
$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );

// --- filter show avatar size ---
$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'thumbnail');

//Panels
$output .= '<div id="panels">'. "\n";

foreach ( $weekdays as $i => $weekday ) {
	
	// --- maybe skip all days but those specified ---
	// 2.3.2: improve days attribute checking logic
	$skip_day = false;
	if ( $atts['days'] ) {
		$days = explode( ',', $atts['days'] );
		$found_day = false;
		foreach ( $days as $day ) {
			$day = trim( $day );
			// 2.3.2: allow for numeric days (0=sunday to 6=saturday)
			if ( is_numeric( $day ) && ( $day > -1 ) && ( $day < 7 ) ) {
				$day = radio_station_get_weekday( $day );
			}
			if ( trim( strtolower( $day ) ) == strtolower( $weekday ) ) {
				$found_day = true;
			}
		}
		if ( !$found_day ) {
			$skip_day = true;
		}
	}

	if ( !$skip_day ) {

		// 2.3.2: set day start and end times
		// 2.3.2: replace strtotime with to_time for timezones
		$day_start_time = radio_station_to_time( $weekdates[$weekday] . ' 00:00' );
		$day_end_time = $day_start_time + ( 24 * 60 * 60 );

		// 2.2.2: use translate function for weekday string
		// 2.3.2: added check for short/long day display attribute
		if ( !in_array( $atts['display_day'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_day'] = 'long';
		}
		if ( 'short' == $atts['display_day'] ) {
			$display_day = radio_station_translate_weekday( $weekday, true );
		} elseif ( ( 'full' == $atts['display_day'] ) || ( 'long' == $atts['display_day'] ) ) {
			$display_day = radio_station_translate_weekday( $weekday, false );
		}

		// 2.3.2: add attribute for date subheading format (see PHP date() format)
		// $subheading = date( 'jS M', strtotime( $weekdate ) );
		if ( $atts['display_date'] ) {
			$date_subheading = radio_station_get_time( $atts['display_date'], $day_start_time );
		} else {
			$date_subheading = radio_station_get_time( 'j', $day_start_time );
		}

		// 2.3.2: add attribute for short or long month display
		$month = radio_station_get_time( 'F', $day_start_time );
		if ( $atts['display_month'] && !in_array( $atts['display_month'], array( 'short', 'full', 'long' ) ) ) {
			$atts['display_month'] = 'short';
		}
		if ( ( 'long' == $atts['display_month'] ) || ( 'full' == $atts['display_month'] ) ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, false );
		} elseif ( 'short' == $atts['display_month'] ) {
			$date_subheading .= ' ' . radio_station_translate_month( $month, true );
		}

		// --- set tab classes ---	
		$weekdate = $weekdates[$weekday];
		//$classes = array( 'day-' . $i );
		$button_classes = array( 'btn','btn-outline-secondary','btn-sm' );
		$dropdown_classes = array( 'dropdown-item' );
		$aria_expanded = 'false';
		if ( $weekdate == $date ) {
			// $classes[] = 'selected-day';
			$button_classes[] = 'active';
			$dropdown_classes[] = 'active';
			$aria_expanded = 'true';
		}
		else
		{
			$button_classes[] = '';
			$dropdown_classes[] = '';
			$aria_expanded = 'false';
		}
		$button_classlist  = implode( ' ', $button_classes );
		$dropdown_classlist  = implode( ' ', $dropdown_classes );
		
		// --- Buttons for Desktop View ---	
		if ( 0 == $i) {
			$button_output = '';
			$button_output .= '<div class="d-none d-md-block d-lg-block">' ."\n";
			$button_output .= '<div class="d-flex justify-content-between">' ."\n";
		}

		$button_output .= '<button class="' . esc_attr( $button_classlist ) . '" type="button" data-toggle="collapse" data-target="#' . strtolower( $weekday ) . '-box" aria-expanded="' . $aria_expanded . '" aria-controls="' . strtolower( $weekday ) . '-box"';
		/*if ( !$atts['display_date'] ) {
			$output .= ' title="' . esc_attr( $date_subheading ) . '"';
		}*/
		$button_output .= '>' . esc_html( $display_day ) . '</button>' . "\n";
		
		if ( $weekday == end($weekdays) )
		{
			$button_output .= '</div></div><br>' . "\n";
		}		
		
		// --- Dropdown for Mobile View ---	
		if ( 0 == $i) {	
			$dropdown_output .= '<div class="d-block d-md-none d-lg-none">' ."\n";
			$dropdown_output .= '<div class="dropdown">' ."\n";
			$dropdown_output .= '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' ."\n";
			$dropdown_output .= 'Day' ."\n";
			$dropdown_output .= '</button>' ."\n";
			$dropdown_output .= '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">' ."\n";
		}		
		
		$dropdown_output .= '<a class="' . esc_attr( $dropdown_classlist ) . '" data-toggle="collapse" href="#' . strtolower( $weekday ) . '-box" data-target="#' . strtolower( $weekday ) . '-box" aria-expanded="' . $aria_expanded . '" aria-controls="' . strtolower( $weekday ) . '-box"';
		/*if ( !$atts['display_date'] ) {
			$output .= ' title="' . esc_attr( $date_subheading ) . '"';
		}*/
		$dropdown_output .= '>' . esc_html( $display_day ) . '</a>' . "\n";
		
		if ( $weekday == end($weekdays) )
		{
			$dropdown_output .= '</div></div></div><br>' . "\n";
		}
		
		// --- get shifts for this day ---
		if ( isset( $schedule[$weekday] ) ) {
			$shifts = $schedule[$weekday];
			//var_dump($shifts);
		} else {
			$shifts = array();
		}

		$foundshows = false;

		// 2.3.0: loop schedule day shifts instead of hours and minutes
		if ( count( $shifts ) > 0 ) {

			$foundshows = true;

			$j = 0;
			foreach ( $shifts as $shift ) {

				$j++;
				$wid = strtolower( $weekday ) . "-box";
				$classes = array( 'collapse'  );
				if ( $weekdate == $date )
				{
					$classes[] = 'show';
				}
				else
				{
					
				}
				$classlist = implode( ' ' , $classes );
				if ( 1 == $j ) {
					$panels .= "\n";
					$panels .= '<div class="' . esc_attr( $classlist ) . '" id="' . $wid . '" data-parent="#panels">';
				}
				$show = $shift['show'];

				$show_link = false;
				if ( $atts['show_link'] ) {
					$show_link = $show['url'];
				}
				$show_link = apply_filters( 'radio_station_schedule_show_link', $show_link, $show['id'], 'tabs' );

				// --- convert shift time data ---
				// $shift_start = radio_station_convert_shift_time( $shift['start'] );
				// $shift_end = radio_station_convert_shift_time( $shift['end'] );
				// $shift_start_time = radio_station_to_time( $shift['day'] . ' ' . $shift_start );
				// $shift_end_time = radio_station_to_time( $shift['day'] . ' ' . $shift_end );
				// if ( $shift_end_time < $shift_start_time ) {
				// 	$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
				// }
				if ( '00:00 am' == $shift['start'] ) {
					$shift_start_time = radio_station_to_time( $weekdate . ' 00:00' );
				} else {
					$shift_start = radio_station_convert_shift_time( $shift['start'] );
					$shift_start_time = radio_station_to_time( $weekdate . ' ' . $shift_start );
				}
				if ( ( '11:59:59 pm' == $shift['end'] ) || ( '12:00 am' == $shift['end'] ) ) {
					$shift_end_time = radio_station_to_time( $weekdate . ' 23:59:59' ) + 1;
				} else {
					$shift_end = radio_station_convert_shift_time( $shift['end'] );
					$shift_end_time = radio_station_to_time( $weekdate . ' ' . $shift_end );
				}

				// --- get split shift real start and end times ---
				// 2.3.2: added for shift display output
				$real_shift_start = $real_shift_end = false;
				if ( isset( $shift['split'] ) && $shift['split'] ) {
					if ( isset( $shift['real_start'] ) ) {
						$real_shift_start = radio_station_convert_shift_time( $shift['real_start'] );
						$real_shift_start = radio_station_to_time( $weekdate . ' ' . $real_shift_start ) - ( 24 * 60 * 60 );
					} elseif ( isset( $shift['real_end'] ) ) {
						$real_shift_end = radio_station_convert_shift_time( $shift['real_end'] );
						$real_shift_end = radio_station_to_time( $weekdate . ' ' . $real_shift_end ) + ( 24 * 60 * 60 );
					}
				}

				// 2.3.0: add genre classes for highlighting
				$classes = array( 'show-row', 'd-flex' );
				
				// 2.3.2: add first and last classes
				if ( 1 == $j ) {
					$classes[] = 'first-show';
				}
				if ( $j == count( $shifts ) ) {
					$classes[] = 'last-show';
				}				
				
				// 2.3.2: check for now playing shift
				if ( ( $now >= $shift_start_time ) && ( $now < $shift_end_time ) ) {
					$classes[] = 'show-active';
				}

				// --- open list item ---
				$classlist = implode( ' ' , $classes );
				$panels .= "\n\n";
				$panels .= '<div class="' . esc_attr( $classlist ) . '">' . "\n";

				// --- Show Image ---
				// (defaults to display on)
				if ( $atts['show_image'] ) {
					// 2.3.0: filter show avatar by show and context
					// 2.3.0: maybe link avatar to show
					$show_avatar = radio_station_get_show_avatar( $show['id'], $avatar_size, ["class" => "img-fluid"] );
					$show_avatar = apply_filters( 'radio_station_schedule_show_avatar', $show_avatar, $show['id'], 'tabs' );
					if ( $show_avatar ) {
						$panels .= '<div class="show-image d-inline-block w-50 ml-1 mr-1">' . "\n";
						if ( $show_link ) {
							$panels .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>' . "\n";
						} else {
							$panels .= $show_avatar;
						}
						$panels .= '</div>' . "\n";
					} else {
						$panels .= '<div class="show-image d-inline-block w-50 ml-1 mr-1">'. "\n";
						//$custom_img = '<img class="img-fluid" width="100" height="100" src="/beta/wp-content/themes/wp-bootstrap-starter-child/custom/img/placeholder.png">' . "\n";
						$custom_img = '<img class="img-fluid" width="100" height="100" src="https://radiodepaul.com/beta/wp-content/uploads/2020/10/radiodepaul_logo.png">' . "\n";
						
						if ( $show_link ) {
							$panels .= '<a href="' . esc_url( $show_link ) . '">' . $custom_img . '</a>' . "\n";
						} else {
							$panels .= $custom_img;
						}
						$panels .= '</div>' . "\n";
					}
				}
				

				// --- Show Information ---
				$panels .= '<div class="show-info d-inline-block w-100">' . "\n";

				// --- show title ---
				if ( $show_link ) {
					$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show['name'] ) . '</a>' . "\n";
				} else {
					$show_title = esc_html( $show['name'] );
				}
				$panels .= '<div class="show-title"><h4>' . "\n";
				$panels .= $show_title;
				$panels .= '</h4></div>' . "\n";
				//$panels .= '</div>';

				// --- show hosts ---
				if ( $atts['show_hosts'] ) {

					$hosts = '';
					if ( $show['hosts'] && is_array( $show['hosts'] ) && ( count( $show['hosts'] ) > 0 ) ) {

						$count = 0;
						$host_count = count( $show['hosts'] );
						//$hosts .= '<span class="show-dj-leader">' . "\n";
			
						//$hosts .= ' </span>';

						foreach ( $show['hosts'] as $host ) {
							$count ++;
							// 2.3.0: added link_hosts attribute check
							if ( $atts['link_hosts'] && !empty( $host['url'] ) ) {
								$hosts .= '<a href="' . esc_url( $host['url'] ) . '">' . esc_html( $host['name'] ) . '</a>';
							} else {
								$hosts .= esc_html( $host['name'] );
							}

							if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
								 || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
								$hosts .= esc_html( __( ',', 'radio-station' ) ) . ' ';
							} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
								$hosts .= ',';
							}
						}
						
						
					}

					$hosts = apply_filters( 'radio_station_schedule_show_hosts', $hosts, $show['id'], 'tabs' );
					if ( $hosts ) {
						$panels .= '<div class="show-host">';
						// phpcs:ignore WordPress.Security.OutputNotEscaped
						$panels .= $hosts;
						$panels .= '</div>' . "\n";
					}
					$panels .= '</div>' . "\n";
				}
			
				// --- show times ---
				if ( $atts['show_times'] ) {

					// --- convert shift time for display ---
					// 2.3.0: updated to use new schedule data
					if ( '00:00 am' == $shift['start'] ) {
						$shift['start'] = '12:00 am';
					}
					if ( '11:59:59 pm' == $shift['end'] ) {
						$shift['end'] = '12:00 am';
					}

					// --- get start and end times ---
					// 2.3.2: maybe use real start and end times
					if ( $real_shift_start ) {
						$start = radio_station_get_time( $start_data_format, $real_shift_start );
					} else {
						$start = radio_station_get_time( $start_data_format, $shift_start_time );
					}
					if ( $real_shift_end ) {
						$end = radio_station_get_time( $end_data_format, $real_shift_end );
					} else {
						$end = radio_station_get_time( $end_data_format, $shift_end_time );
					}
					$start = radio_station_translate_time( $start );
					$end = radio_station_translate_time( $end );

					// 2.3.0: filter show time by show and context
					$show_time = '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
					$show_time .= '<span class="rs-sep"> ' . esc_html( __( '-', 'radio-station' ) ) . ' </span>' . "\n";
					$show_time .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";
					$show_time = apply_filters( 'radio_station_schedule_show_time', $show_time, $show['id'], 'tabs' );

					$panels .= '<div class="show-time d-inline-block w-100 text-right align-self-center">' . "\n" . $show_time . '</div>' . "\n";

				} else {
				
					// 2.3.2: added for now playing check
					$panels .= '<span class="rs-time rs-start-time" data="' . esc_attr( $shift_start_time ) . '" data-format="H:i"></span>' . "\n";
					$panels .= '<span class="rs-time rs-end-time" data="' . esc_attr( $shift_end_time ) . '" data-format="H:i"></span>' . "\n";

				}
				
				// 2.3.2: last classes
				if ( $j == count( $shifts ) ) 
				{
					$panels .= '</div>' . "\n";
					$panels .= '</div>' . "\n";
				}
				else
				{
					$panels .= '</div>' . "\n";
				}	
			}
		}

		if ( !$foundshows ) {
			// 2.3.2: added no shows class
			$panels .= '<div class="show-row no-shows">';
			$panels .= esc_html( __( 'No Shows scheduled for this day.', 'radio-station' ) );
			$panels .= '</div>';
			$panels .= "\n\n";
		}
	}
}
$output .= $button_output;
$output .= $dropdown_output;
$output .= "\n\n";
$output .= $panels;
$output .= '</div>';

if ( isset( $_GET['rs-shift-debug'] ) && ( '1' == $_GET['rs-shift-debug'] ) ) {
	$output .= $shiftdebug;
}