<?php 
/* 
 * Template Name: Schedule 
 * Template for retrieving schedule
 * Author : Matthew Mola
 * Date: 10/31/2020
 * Ref: http://nlb-creations.com/2014/06/06/radio-station-tutorial-creating-a-tabbed-programming-schedule/
*/ 
 ?>

<?php get_header(); ?>

<div id="primary" class="content-area container">
<main id="main" class="site-main" role="main">
<?php

// Start the loop.
while ( have_posts() ) : the_post();
{
?>
	<header class="entry-header">
		<?php $output .= the_title( '<h1 class="text-uppercase">', '</h1>' ); ?>
	</header><!-- .entry-header -->
	<?php
	// --- get all the required info ---
	get_template_part( 'template-parts/content', 'page' );

	// --- get all the required info ---
	//date_default_timezone_set('America/Chicago'); 
	$schedule = radio_station_get_current_schedule();
	//var_dump($schedule);
	$hours = radio_station_get_hours();
	$now = radio_station_get_now();
	$date = radio_station_get_time( 'date', $now );
	$today =  radio_station_get_time( 'day', $now );

	// --- set shift time formats ---
	$start_data_format = $end_data_format = 'g:i a';
	$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format );
	$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format );

	// --- get schedule days and dates ---
	$weekdays = radio_station_get_schedule_weekdays();
	$weekdates = radio_station_get_schedule_weekdates( $weekdays, $now );

	// --- filter show avatar size ---
	$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'medium' );

	// --- Create Schedule Loop ---
	foreach ( $weekdays as $i => $weekday ) {
		
		// --- Create Panels Div View ---	
		if ( 0 == $i) {
			$output .= '<div id="panels">'. "\n";
		}

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

		// --- Set Dropdown and Button classes ---	
		$weekdate = $weekdates[$weekday];
		$wid = strtolower( $weekday ) . "-schedule";
		$button_classes = array( 'btn','btn-outline-secondary','btn-sm' );
		$dropdown_classes = array( 'dropdown-item' );
		$aria_expanded = 'false';
		if ( $weekdate == $date ) {
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
			$button_output = '<div class="d-none d-md-block d-lg-block">' ."\n";
			$button_output .= '<div class="d-flex justify-content-between">' ."\n";
		}

		$button_output .= '<button class="' . esc_attr( $button_classlist ) . '" type="button" data-toggle="collapse" data-target="#' . $wid . '" aria-expanded="' . $aria_expanded . '" aria-controls="' . $wid . '"';
		$button_output .= '>' . esc_html( $display_day ) . '</button>' . "\n";
		
		if ( $weekday == end($weekdays) )
		{
			$button_output .= '</div></div><br>' . "\n";
		}
		
		// --- Dropdown for Mobile View ---	
		if ( 0 == $i) {	
			$dropdown_output = '<div class="d-block d-md-none d-lg-none">' ."\n";
			$dropdown_output .= '<div class="dropdown">' ."\n";
			$dropdown_output .= '<button class="btn btn-rdp dropdown-toggle" type="button" id="dropdownDay" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' ."\n";
			$dropdown_output .= 'Day' ."\n";
			$dropdown_output .= '</button>' ."\n";
			$dropdown_output .= '<div class="dropdown-menu" aria-labelledby="dropdownDay">' ."\n";
		}		
		
		$dropdown_output .= '<a class="' . esc_attr( $dropdown_classlist ) . '" data-toggle="collapse" href="#' . $wid . '" data-target="#' . $wid . '" aria-expanded="' . $aria_expanded . '" aria-controls="' . $wid . '"';
		$dropdown_output .= '>' . esc_html( $display_day ) . '</a>' . "\n";
		
		if ( $weekday == end($weekdays) )
		{
			$dropdown_output .= '</div></div></div><br>' . "\n";
		}
		
		// --- get shifts for this day ---
		if ( isset( $schedule[$weekday] ) ) {
			$shifts = $schedule[$weekday];
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
				$classes = array( 'collapse'  );
				if ( $weekdate == $date )
				{
					$classes[] = 'show';
				}
				$classlist = implode( ' ' , $classes );
				
				// --- get all the required info ---
				$show = $shift['show'];
				$show_name = $show['name'];
				$show_id = $show['id'];
				$show_link = $show['url'];
				$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size, ["class" => "img-fluid"] );
				$host_array = get_post_meta( $show_id, 'show_user_list', true );
				
				//High Resolution Avatar Fix
				if(stripos($show_avatar, "\"1\" height=\"1\"") !== false)
				{
					$show_avatar = '<img width="500" height="500" src="' . radio_station_get_show_avatar_url( $show_id ) . '" class="img-fluid" alt=""/>';
				}	
				
				// --- Get Title ---
				if ( $show_link ) {
					$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show_name ) . '</a>' . "\n";
				} else {
					$show_title = esc_html( $show_name );
				}
				
				// --- Get Show Image ---
				if ( ! $show_avatar ) {
					$rdp_logo = wp_get_attachment_image_src( '300', $avatar_size )[0];
					//$placeholder_src = '/beta/wp-content/themes/wp-bootstrap-starter-child/custom/img/placeholder.png';
					$show_avatar = '<img class="img-fluid" alt="Radio DePaul Logo" src="'. $rdp_logo .'">' . "\n";
				}
				
				// --- Get Show Hosts ---
				if ( $host_array ) {

					$hosts = '';
					if ( $host_array && is_array( $host_array ) && ( count( $host_array ) > 0 ) ) {

						$count = 0;
						$host_count = count( $host_array );

						foreach ( $host_array as $host ) {
							$count ++;
							$user_info = get_userdata( $host );
							$hosts .= $user_info->display_name;
							

							if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
								 || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
								$hosts .= esc_html( __( ',' ) ) . ' ';
							} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
								$hosts .= ',';
							}
						}
						
						
					}
				}
				
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
				
				// --- Get Shift Time
				if (( $shift['start'] ) &&  ( $shift['end'] )) 
				{
					// --- convert shift time for display ---
					if ( '00:00 am' == $shift['start'] ) {
						$shift['start'] = '12:00 am';
					}
					if ( '11:59:59 pm' == $shift['end'] ) {
						$shift['end'] = '12:00 am';
					}

					// --- get start and end times ---
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

					$show_time = '<span class="rs-time rs-start-time" data-shift-start="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
					$show_time .= '<span class="rs-sep"> ' . esc_html( __( '-' ) ) . ' </span>' . "\n";
					$show_time .= '<span class="rs-time rs-end-time" data-shift-end="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";
				}
	
				// --- Create Panels Div ---
				if ( 1 == $j ) {
					$panels .= "\n";
					$panels .= '<div class="' . esc_attr( $classlist ) . '" id="' . $wid . '" data-parent="#panels">';
				}

				// --- Classes ---
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

				// --- Show Row ---
				$classlist = implode( ' ' , $classes );
				$panels .= "\n\n";
				$panels .= '<div class="' . esc_attr( $classlist ) . '">' . "\n";

				// --- Show Image Box ---
				$panels .= '<div class="show-image col-md-2 col-4 align-self-center ml-1 mr-1">' . "\n";
				if ( $show_link ) {
					$panels .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>' . "\n";
				} else {
					$panels .= $show_avatar;
				}
				$panels .= '</div>' . "\n";

				// --- Show Information Box ---
				$panels .= '<div class="show-info col-md-6 col-8 align-self-center">' . "\n";

				// --- Show Title ---
				$panels .= '<div class="show-title">' . $show_title . '</div>' . "\n";
				
				// --- Show Host ---
				if ( $hosts ) {
					$panels .= '<div class="show-host d-none d-sm-block">';
					$panels .= $hosts;
					$panels .= '</div>' . "\n";
				}
				
				// --- Show Times Box Mobile ---
				$panels .= '<div class="show-time d-block d-sm-none">' . "\n" . $show_time . '</div>' . "\n";
				
				// --- Close Show Information Box ---
				$panels .= '</div>' . "\n";
				
				// --- Show Times Box Desktop ---
				$panels .= '<div class="show-time col-md-4 d-none d-sm-block align-self-center text-right">' . "\n" . $show_time . '</div>' . "\n";
				
				// --- Close Show Box & Weekday Box  ---
				if ( $j == count( $shifts ) ) 
				{
					$panels .= '</div>' . "\n";
					$panels .= '</div>' . "\n";
				}
				// --- Close Show Row Box ---
				else
				{
					$panels .= '</div>' . "\n";
				}				
			}
		}

		// --- No Shows for a Weekday --- 
		if ( !$foundshows ) {
			$classes = array( 'collapse'  );
			if ( $weekdate == $date )
			{
				$classes[] = 'show';
			}
			$classlist = implode( ' ' , $classes );
			
			$panels .= "\n";
			$panels .= '<div class="' . esc_attr( $classlist ) . '" id="' . $wid . '" data-parent="#panels">' . "\n";
			$panels .= '<div class="show-row d-flex justify-content-center no-shows">' . "\n";
			$panels .= esc_html( __( 'No Shows scheduled for this day' ) ) . "\n";;
			$panels .= '</div>' . "\n";
			$panels .= '</div>' . "\n";
		}
			
		// --- Panels Div View Close ---
		if ( $weekday == end( $weekdays ) ) 
		{
			$panels .= '</div>' . "\n";
		}
	}
	// --- End of Schedule Loop --
	$output .= $button_output;
	$output .= $dropdown_output;
	$output .= "\n\n";
	$output .= $panels;
	echo $output;
}
// End of the loop.
endwhile;
?>
</main><!-- .site-main -->
<?php get_sidebar( 'content-bottom' ); ?>	
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>