<?php
/* 
 * Template Name: Shows 
 * Template for retrieving shows with genres
 * Author : Matthew Mola
 * Date: 10/31/2020
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
	$hours = radio_station_get_hours();
	$now = radio_station_get_now();
	$date = radio_station_get_time( 'date', $now );
	$today =  radio_station_get_time( 'day', $now );
	$weekdays = radio_station_get_schedule_weekdays();
	$now = radio_station_get_now();
	$genres_data = radio_station_get_genres_data();
	//array_unshift($genres_data, 'All');
	$genres_data = array('All' => array()) + $genres_data;

	// --- set shift time formats ---
	$start_data_format = $end_data_format = 'g:i a';
	$start_data_format = apply_filters( 'radio_station_time_format_start', $start_data_format );
	$end_data_format = apply_filters( 'radio_station_time_format_end', $end_data_format );

	// --- filter show avatar size ---
	$avatar_size = apply_filters( 'radio_station_schedule_show_avatar_size', 'medium' );	
	
	// --- Create Genre Shows Loop ---
	foreach ( $genres_data as $gtype => $genre ) 
	{
		// Change variables for All Genres
		if($gtype == 'All')
		{
			$genre_list[] = $gtype;
			$genre_shows = radio_station_get_shows(); 
		}
		else
		{
			$genre_list[] = $gtype;
			$genre_shows = $genre['shows'];
		
			//Alphabetical Sort
			array_multisort(array_column($genre_shows, 'name'), SORT_NATURAL, $genre_shows);
		}
		if(!empty ($genre_shows) )
		{
			foreach ($genre_shows as $skey => $show) 
			{			
				// --- get all the required info ---
				if($gtype == 'All')
				{
					$show_id = $show->ID;
					$show_link = get_permalink( $show_id );
				}
				else
				{
					$show_id = $show['id'];	
					$show_link = $show['url'];
				}
				$show_name = get_the_title( $show_id );
				$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size, ["class" => "img-fluid"] );
				$host_array = get_post_meta( $show_id, 'show_user_list', true );				
				//$title = $show->post_title;
				//$title = $titles[$i]['post_title'];
				//var_dump($title);
				
				//High Resolution Avatar Fix
				if(stripos($show_avatar, "\"1\" height=\"1\"") !== false)
				{
					$show_avatar = '<img width="500" height="500" src="' . radio_station_get_show_avatar_url( $show_id ) . '" class="img-fluid" alt=""/>';
				}

				// --- Create Genre Boxes ---	
				$gid = strtolower( $gtype ) . "-shows";
				$classes = array( 'collapse'  );
				if($gtype == 'All') { $classes[] = 'show'; }
				$classlist = implode( ' ' , $classes );
				if ( 0 == $skey ) {
					$panels .= "\n";
					$panels .= '<div class="' . esc_attr( $classlist ) . '" id="' . $gid . '" data-parent="#panels">';
				}		
			
				// --- Show Row ---
				$classes = array( 'show-row', 'd-flex' );
				$classlist = implode( ' ' , $classes );
				$panels .= "\n\n";
				$panels .= '<div class="' . esc_attr( $classlist ) . '">' . "\n";

				
				// --- Get Show Image ---
				if ( ! $show_avatar ) {
					$rdp_logo = wp_get_attachment_image_src( '300', $avatar_size )[0];
					//$placeholder_src = '/beta/wp-content/themes/wp-bootstrap-starter-child/custom/img/placeholder.png';
					$show_avatar = '<img class="img-fluid" alt="Radio DePaul Logo" src="'. $rdp_logo .'">' . "\n";
				}

				// --- Show Image Box ---
				$panels .= '<div class="show-image col-md-2 col-4 ml-1 mr-1">' . "\n";
				if ( $show_link ) {
					$panels .= '<a href="' . esc_url( $show_link ) . '">' . $show_avatar . '</a>' . "\n";
				} else {
					$panels .= $show_avatar;
				}
				$panels .= '</div>' . "\n";	

				// --- Show Information ---
				$panels .= '<div class="show-info col-md-6 col-8 align-self-center">' . "\n";

				// --- Get Title ---
				if ( $show_link ) {
					$show_title = '<a href="' . esc_url( $show_link ) . '">' . esc_html( $show_name ) . '</a>' . "\n";
				} else {
					$show_title = esc_html( $show_name );
				}
				$panels .= '<div class="show-title">' . "\n";
				$panels .= $show_title;
				$panels .= '</div>' . "\n";
				
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
								$hosts .= esc_html( __( ', ' ) ) . ' ';
							} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
								$hosts .= ', ';
							}
						}	
					}
					// --- Show Host ---
					if ( $hosts ) {
						$panels .= '<div class="show-host">';
						$panels .= $hosts;
						$panels .= '</div>' . "\n";
					}
				}
				$panels .= '</div>' . "\n";
				
				// --- Show Times Box ---
				$shifts = radio_station_get_show_schedule( $show_id );
				$panels .= '<div class="show-times col-md-4 d-none d-sm-block align-self-center text-right">' . "\n";
				//$panels .= '<div class="show-time d-inline-block w-100 text-right align-self-stretch">' . "\n" . $show_time . '</div>' . "\n";

				$found_encore = false;

				foreach ( $weekdays as $day ) {
					$show_times = array();
					if ( $shifts && is_array( $shifts ) && ( count( $shifts ) > 0 ) ) {
						foreach ( $shifts as $shift ) {
							if ( $day == $shift['day'] ) {

								// --- convert shift info ---
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
									$show_time .= '<span class="show-encore">*</span>';
								}
								$show_time .= '</span>';
								$show_times[] = $show_time;
							}
						}
					}
					$show_times_count = count( $show_times );
					if ( $show_times_count > 0 ) {
						
						$panels .= '<span class="show-day ' . strtolower( $day ) . '">';
						$weekday = radio_station_translate_weekday( $day, true );
						$panels .= esc_html( $weekday ) . '@ ';
						$panels .= '</span>' . "\n";
						foreach ( $show_times as $s => $show_time ) {
							$panels .= $show_time . '<br>' . "\n";
							// if ( $i < ( $show_times_count - 1 ) ) {
							//	$blocks['show_times'] .= '<br>';
							// }
						}
					}
				}
				
				// --- Close Genre Box and Show Row ---
				$panels .= '</div>' . "\n";
				$panels .= '</div>' . "\n";	
				
				// --- Genre Boxes Close ---
				if ( !next ( $genre_shows ) )
				{
					$panels .= '</div>' . "\n";
				}
			}
		
		}		
	}
	// --- End of Genre Shows Loop --	
	
	// Create Buttons and Dropdown Loop
	foreach ($genre_list as $ct => $genre_button ) 
	{
		$button_classes = array( 'btn','btn-outline-secondary','btn-sm' );
		$dropdown_classes = array( 'dropdown-item' );
		$aria_expanded = 'false';
		$genre_title = $genre_button;
		if ( $genre_button == "All" ) 
		{
			// $classes[] = 'selected-day';
			$button_classes[] = 'active';
			$dropdown_classes[] = 'active';
			$aria_expanded = 'true';
			$genre_title = 'All Shows';
		}
		else
		{
			$button_classes[] = '';
			$dropdown_classes[] = '';
			$aria_expanded = 'false';
		}
		$gid = strtolower( $genre_button ) . "-shows";
		$button_classlist  = implode( ' ', $button_classes );
		$dropdown_classlist  = implode( ' ', $dropdown_classes );

		// --- Buttons for Desktop View ---	
		if( 0 == $ct) {
			$button_output = '<div class="d-none d-md-block d-lg-block">' ."\n";
			$button_output .= '<div class="d-flex justify-content-between">' ."\n";
		}

		$button_output .= '<button class="' . esc_attr( $button_classlist ) . '" type="button" data-toggle="collapse" data-target="#' . strtolower( $gid ) . '" aria-expanded="' . $aria_expanded . '" aria-controls="' . strtolower( $gid ) . '"';
		$button_output .= '>' . esc_html( $genre_title ) . '</button>' . "\n";

		if ( $genre_button == end($genre_list) )
		{
			$button_output .= '</div></div><br>' . "\n";
		}

		// --- Dropdown for Mobile View ---	
		if( 0 == $ct) {
			$dropdown_output = '<div class="d-block d-md-none d-lg-none">' ."\n";
			$dropdown_output .= '<div class="dropdown">' ."\n";
			$dropdown_output .= '<button class="btn btn-rdp dropdown-toggle" type="button" id="dropdownGenre" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' ."\n";
			$dropdown_output .= 'Genre' ."\n";
			$dropdown_output .= '</button>' ."\n";
			$dropdown_output .= '<div class="dropdown-menu" aria-labelledby="dropdownGenre">' ."\n";
		}

		$dropdown_output .= '<a class="' . esc_attr( $dropdown_classlist ) . '" data-toggle="collapse" href="#' . strtolower( $gid ) . '" data-target="#' . strtolower( $gid ) . '" aria-expanded="' . $aria_expanded . '" aria-controls="' . strtolower( $gid ) . '"';
		$dropdown_output .= '>' . esc_html( $genre_title ) . '</a>' . "\n";
		
		if ( $genre_button == end($genre_list) )
		{
			$dropdown_output .= '</div></div></div><br>' . "\n";
		}
	}
	// --- End of All Shows Loop --
	
	// --- Create Panels Div View ---	
	$output .= '<div id="panels">'. "\n";
	$output .= $button_output;
	$output .= $dropdown_output;
	$output .= "\n\n";
	$output .= $panels;
	
	// --- Panels Div View Close ---
	$output .= '</div>';
	
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