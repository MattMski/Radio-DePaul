<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
/**
 * WP Bootstrap Starter functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WP_Bootstrap_Starter_child
 */

// ---------------------------
// Add Admin Bar Add New Links
// ---------------------------
// 2.2.2: re-add new post type items to admin bar
// (as no longer automatically added by register_post_type)
// 2.3.0: fix to function prefix (was station_radio_)
// 2.3.0: change priority to be after main new content iteme
add_action( 'admin_bar_menu', 'fixmenu', 70 );
function fixmenu( $wp_admin_bar ) {

	// 2.3.0: loop post types to add post type items
	$post_types = array( RADIO_STATION_SHOW_SLUG );
	foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$args = array(
				'id'     => 'new-' . $post_type,
				'title'  => $post_type_object->labels->singular_name,
				'parent' => 'new-content',
				'href'   => admin_url( 'post-new.php?post_type=' . $post_type ),
			);
			$wp_admin_bar->add_node( $args );
	}
}

/**
 * Enqueue RDP CSS and JS
 */
function enqueue() 
{
	wp_enqueue_style( 'RDP', get_stylesheet_directory_uri() . '/RDP.css',false,'1.3','all');
	wp_enqueue_script( 'RDP', get_stylesheet_directory_uri() . '/RDP.js', false, '1.2', true);
}
add_action( 'wp_head', 'enqueue' ); // default priority: 10

//Google Analytics
add_action('wp_head', 'wpb_add_googleanalytics');
function wpb_add_googleanalytics() { 
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-M7WNFRFSW7"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'G-M7WNFRFSW7');
</script>
<?php 
}

/**
 * Remove the username tag from the Twitter card necessary for Slack
 * 
 * @param Array $tag_arr ( meta tag key value pairs )
 * 
 * @return Array $tag_arr
 */
function yoast_remove_username_metatag( $tag_arr ) {
	
if( isset( $tag_arr['Written by'] ) ) {
	unset( $tag_arr['Written by'] );
}

return $tag_arr;
	
}
add_filter( 'wpseo_enhanced_slack_data', 'yoast_remove_username_metatag' );

if ( function_exists( 'coauthors_posts_links' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function wp_bootstrap_starter_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$posted_on = sprintf(
		esc_html_x( 'Posted on %s', 'post date', 'wp-bootstrap-starter' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'wp-bootstrap-starter' ),
		coauthors_posts_links( null, null, null, null, false )
	);

	echo '<span class="posted-on">' . $posted_on . '</span> | <span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

    if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
        echo ' | <span class="comments-link"><i class="fa fa-comments" aria-hidden="true"></i> ';
        /* translators: %s: post title */
        comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'wp-bootstrap-starter' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
        echo '</span>';
    }

}
endif;

//remove links to show from show posts and playlists
remove_filter( 'the_content', 'radio_station_add_show_links', 20 );

//Remove url
function wp_bootstrap_starter_entry_footer()
{
	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'wp-bootstrap-starter' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}

/**
 * Removes the `profile.php` admin color scheme options
 */
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

if ( ! function_exists( 'cor_remove_personal_options' ) ) {
  /**
   * Removes the leftover 'Visual Editor', 'Keyboard Shortcuts' and 'Toolbar' options.
   */
  function cor_remove_personal_options( $subject ) {
    $subject = preg_replace( '#<h3>Personal Options</h3>.+?/table>#s', '', $subject, 1 );
    return $subject;
  }

  function cor_profile_subject_start() {
    ob_start( 'cor_remove_personal_options' );
  }

  function cor_profile_subject_end() {
    ob_end_flush();
  }
}
add_action( 'admin_head-profile.php', 'cor_profile_subject_start' );
add_action( 'admin_footer-profile.php', 'cor_profile_subject_end' );

/**
 * Upcoming Shows
 */
if ( ! function_exists( 'getSchedule_func' ) ) 
{
	function getSchedule_func()
	{
		if(!empty(radio_station_get_current_show()))
		{
			//var_dump(radio_station_get_current_show());
			//unset($next_shows[2]);
			$cshow = radio_station_get_current_show();
			$cshow = array(0 => $cshow); 
			$nshow = radio_station_get_next_shows();
			unset($nshow[2]);
			$schedule = array_merge($cshow, $nshow);
			//Current
			$string =  '<div class="row">' . "\n";
			$string .= '<div class="col-md-4"><h2>On Air</h2></div>' . "\n";
			$string .= '<div class="col-md-8 d-none d-sm-block"><h2>Up Next</h2></div>' . "\n";
			$string .= '</div>' . "\n";
		}
		else
		{
			$schedule = radio_station_get_next_shows();	
			//print_r( $next_shows );
			//Just print next shows
			$string = "<h2>Up next</h2>" . "\n";
		}
		
		$string .= "<div class='row getSchedule'>" . "\n";
		foreach ( $schedule as $key => $show ) {
			//print_r($show);
			//[day] => Saturday
			//[date] => 2020-11-28
			$shift = $show;
			$day = $show['day'];
			$show_id = $show['show']['id'];
			$show_name = $show['show']['name'];
			$show_url = $show['show']['url'];
			$show_hosts = $show['show']['hosts'];
			//$show_avatar = $show['show']['avatar_url'];
			$show_avatar = radio_station_get_show_avatar( $show_id, 'medium', ["class" => "img-fluid"] );
			$start_data_format = $end_data_format = 'g:i a';
			
			//High Resolution Avatar Fix
			if(stripos($show_avatar, "\"1\" height=\"1\"") !== false && $show_avatar)
			{
				$show_avatar = '<img width="500" height="500" src="' . radio_station_get_show_avatar_url( $show_id ) . '" class="img-fluid" alt="' . $show_name . ' Show Logo"/>';
			}
			
			//Small Image Fix
			$doc = new DOMDocument();
			$doc->loadHTML($show_avatar);
			$xpath = new DOMXPath($doc);
			$src = $xpath->evaluate("string(//img/@src)"); # "/images/image.jpg"
			if ((stripos($src, '500x500') === false) && $show_avatar)
			{
				$show_avatar = '<img width="500" height="500" src="' . $src . '" class="img-fluid" alt="' . $show_name . ' Show Logo"/>';
			}
			
			//Set Alt Text if None
			$alt = $xpath->evaluate("string(//img/@alt)");
			if (empty($alt) && $show_avatar)
			{
				update_post_meta(radio_station_get_show_avatar_id($show_id), '_wp_attachment_image_alt', $show_name . " Show Logo");
			}
			//update_post_meta(radio_station_get_show_avatar_id($show_id), '_wp_attachment_image_alt', $show_name . " Show Logo");
			
			//Set Radio DePaul Logo if no logo
			if ( ! $show_avatar ) {
				$rdp_logo = wp_get_attachment_image_src( '300', 'medium' )[0];
				//$placeholder_src = '/beta/wp-content/themes/wp-bootstrap-starter-child/custom/img/placeholder.png';
				$show_avatar = '<img class="img-fluid" alt ="Radio DePaul Logo" src="'. esc_url ( $rdp_logo ) .'">' . "\n";
			}
			
			$hosts = '';
			if ( $show_hosts && is_array( $show_hosts ) && ( count( $show_hosts ) > 0 ) ) {

				$count = 0;
				$host_count = count( $show_hosts );

				foreach ( $show_hosts as $host ) {
					$count ++;
					//$user_info = get_userdata( $host );
					//$hosts .= $user_info->display_name;
					$hosts .= $host['name'];				

					if ( ( ( 1 === $count ) && ( 2 === $host_count ) )
						 || ( ( $host_count > 2 ) && ( ( $host_count - 1 ) === $count ) ) ) {
						$hosts .= esc_html( __( ',' ) ) . ' ';
					} elseif ( ( $count < $host_count ) && ( $host_count > 2 ) ) {
						$hosts .= ',';
					}
				}	
			}
			else
			{
				$hosts .= '<br>';
			}
			
			// --- set shift start and end ---
			if ( isset( $shift['real_start'] ) ) {
				$start = $shift['real_start'];
			} elseif ( isset( $shift['start'] ) ) {
				$start = $shift['start'];
			} else {
				$start = $shift['start_hour'] . ':' . $shift['start_min'] . ' ' . $shift['start_meridian'];
			}
			if ( isset( $shift['real_end'] ) ) {
				$end = $shift['real_end'];
			} elseif ( isset( $shift['end'] ) ) {
				$end = $shift['end'];
			} else {
				$end = $shift['end_hour'] . ':' . $shift['end_min'] . ' ' . $shift['end_meridian'];
			}

			// --- convert shift info ---
			// 2.2.2: fix to weekday value to be translated
			// 2.3.2: replace strtotime with to_time for timezones
			// 2.3.2: use exact shift date in time calculations
			// 2.3.2: fix to convert to 24 hour format first
			// $display_day = radio_station_translate_weekday( $shift['day'] );
			$shift_start = radio_station_convert_shift_time( $start );
			$shift_end = radio_station_convert_shift_time( $end );
			if ( isset( $shift['real_start'] ) ) {
				$prevday = radio_station_get_previous_day( $shift['day'] );
				$shift_start_time = radio_station_to_time( $weekdates[$prevday] . ' ' . $shift_start );
			} else {
				$shift_start_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $shift_start );
			}
			$shift_end_time = radio_station_to_time( $weekdates[$shift['day']] . ' ' . $shift_end );
			if ( $shift_end_time < $shift_start_time ) {
				$shift_end_time = $shift_end_time + ( 24 * 60 * 60 );
			}

			// --- maybe set next show shift times ---
			if ( !isset( $next_start_time ) ) {
				$next_start_time = $shift_start_time;
				$next_end_time = $shift_end_time;
			}

			// --- get shift display times ---
			// 2.3.2: use time formats with translations
			$start = radio_station_get_time( $start_data_format, $shift_start_time );
			$end = radio_station_get_time( $end_data_format, $shift_end_time );
			$start = radio_station_translate_time( $start );
			$end = radio_station_translate_time( $end );
			$weekday = radio_station_translate_weekday( $day, true );

			// --- set shift display output ---
			$shift_display = esc_html( $weekday ) . '@ ';
			$shift_display .= '<span class="rs-time rs-start-time" data-shift-start="' . esc_attr( $shift_start_time ) . '" data-format="' . esc_attr( $start_data_format ) . '">' . esc_html( $start ) . '</span>' . "\n";
			$shift_display .= '<span class="rs-sep"> - </span>' . "\n";
			$shift_display .= '<span class="rs-time rs-end-time" data-shift-end="' . esc_attr( $shift_end_time ) . '" data-format="' . esc_attr( $end_data_format ) . '">' . esc_html( $end ) . '</span>' . "\n";

			// Display Up Next text on mobile only if there is a current show after the current show
			if($key == 1 && !empty(radio_station_get_current_show()))
			{
				$string .= '<div class="col-md-8 d-block d-md-none d-lg-none"><h2>Up Next</h2></div>' . "\n";	
			}
			
			$string .= "<div class='col-xs-12 col-md-4 d-flex align-items-end'>" . "\n";
			$string .= '<a href="' . esc_url( $show_url ) . '">' . "\n";
			$string .= "<div class='show-avatar'>\n" . $show_avatar  . "</div>\n";  
			$string .= "<div class='show-box flex-column px-2 py-2 my-1'>\n";
			$string .= "<div class='show-name py-1'><b>\n" . $show_name . "</b></div>\n";
			$string .= "<div class='show-hosts py-1'>\n" . $hosts . "</div>\n"; 
			$string .= "<div class='show-shift py-1'>\n" . $shift_display . "</div>\n"; 
			$string .= "</div>\n";
			$string .= '</a>' . "\n";			
			$string .= "</div>\n\n"; 
		}
		$string .= "</div>\n"; 

		return $string;
	}
}
add_shortcode( 'getSchedule', 'getSchedule_func' );

/**
 * Get Managers Function
 */
if ( ! function_exists( 'getManagers_func' ) ) 
{
	function getManagers_func()
	{			
		//Managers Page ID is 401
		$content = get_post(401)->post_content;
		$regex = '/^<.*[0-9]+-[0-9]+.*>$/m';
		//Check for year-year and split the content into an array
		$manager_people = preg_split($regex, $content);
		unset($manager_people[0]);
		$manager_people = array_values($manager_people);
		//Set to current year
		$manager_people = $manager_people[0];
		
		//Get manager people per year index
		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($manager_people, 'HTML-ENTITIES', 'UTF-8'));
		$paragraphs = $dom->getElementsByTagName('p');

		$button = '<div class="row d-flex mx-auto"><a class="btn btn-secondary" href="./managers" target="_blank" role="button">See All</a></div>';
		$string .= '<div class="row">' . "\n";	
		
		// --- Create User Loop ---
		$count = 1;
		foreach ($paragraphs as $user)
		{
			if(strpos($user->textContent, " : ") !== false)
			{
				$pieces = explode(" : ", $user->textContent);
			}
			
			$args= array(
			  'search' => $pieces[0], // or login or nicename in this example
			  'search_fields' => array('user_login','user_nicename','display_name')
			);
			$get_userid = (new WP_User_Query($args))->results[0]->ID;
			$user_id = $get_userid;
			$user_info = get_userdata($user_id);
			$user_display_name = $user_info->display_name;
			if (! $user_display_name) { $user_display_name = $pieces[0]; } 
			//$avatar = get_avatar_url($user_id);
			$avatar = get_wpupa_url($user_id, ['size' => 'medium']);
			$station_position = $pieces[1];
			//$station_position = get_the_author_meta('rdp_role', $user_id);
			$string .= "<div class='col-xs-12 col-md-4'>" . "\n";
			//$string .= "<img class='img-fluid' src='" . esc_html( get_avatar_url($user,  array("size"=>500)) )  . "'>\n";   
			//$string .= '<div class="manager-avatar img-fluid">' . get_avatar($user_id, '500', '', '', $args = array( 'class' => 'img-fluid rdp_managers')) . '</div>'  . "\n";
			$string .= '<div class="manager-avatar"><img class="img-fluid" alt="' . $user_display_name . "'s Picture" . '" src="' . $avatar . '"></div>' . "\n";			
			$string .= '<div class="manager-box">' . "\n";				
			$string .= "<div class='manager-name'>" . esc_html( $user_display_name  )  . "</div>" . "\n";
			$string .= "<div class='manager-position'>" . $station_position . "</div>" . "\n"; 
			$string .= "</div>" . "\n";
			$string .= "</div>" . "\n";
			if (++$count > 6) break;
		}
		
		$string .= $button;
		$string .= "</div>" . "\n"; 
		
		return $string;
	}
}
add_shortcode( 'getManagers', 'getManagers_func' );

/**
 * Get Awards Function
 */
if ( ! function_exists( 'getAwards_func' ) ) 
{
	function getAwards_func()
	{
		//Awards Page ID is 316
		$content = get_post(316)->post_content;
		$regex = '/^<.*>[0-9]+<.*>$/m';
		//Check for year and split the content into an array
		$awards = preg_split($regex, $content);
		unset($awards[0]);
		$awards = array_values($awards);
		//Set to current year
		$awards = $awards[0];
		
		//Get award per year index
		$dom = new DOMDocument();
		$dom->loadHTML(mb_convert_encoding($awards, 'HTML-ENTITIES', 'UTF-8'));
		$paragraphs = $dom->getElementsByTagName('p');
		
		$button = '<div class="row d-flex mx-auto pt-1"><a class="btn btn-secondary" href="./awards" target="_blank" role="button">See All</a></div>' . "\n";
		//$string .= '<div class="row">' . "\n";	
		$string .= '<div class="row d-flex justify-content-between">' . "\n";
		
		// --- Create User Loop ---
		foreach ($paragraphs as $award)
		{
			if(strpos($award->textContent, " : ") !== false)
			{
				$pieces = explode(" : ", $award->textContent);
				
				if(count($paragraphs) <= 2)
				{
					$string .= '<div class="col-md-6 col-12 d-flex">' . "\n";	
				}
				else
				{
					$string .= '<div class="col-md-3 col-12 d-flex">' . "\n";	
				}		
				$string .= '<div class="flex-fill rdp-black text-white pl-2 pr-2 mb-2">';
				$string .= '<span class="award-year">' . $pieces[0] . '</span><br>' . "\n";
				$string .= '<span class="award-name">' . $pieces[1] . '</span>' . "\n";
				$string .= '<br><br>';
				$string .= '</div>' . "\n";	
				$string .= '</div>' . "\n";
			}
		}
		
		$string .= $button;
		$string .= "</div>" . "\n"; 
		
		return $string;
	}
}
add_shortcode( 'getAwards', 'getAwards_func' );

/**
 * Get Sponsors Function
 */
if ( ! function_exists( 'getSponsors_func' ) ) 
{
	function getSponsors_func()
	{
		//Sponsors Page ID is 462
		$post = get_post(462)->post_content;
		//$title = get_the_title(462);
		$title = 'Sponsors';
		$dom = new DOMDocument();
		$dom->loadHTML($post);
		//$imgs = $dom->getElementsByTagName('img');
		$figures = $dom->getElementsByTagName('figure');

		$classes = array();
		if ( count($figures) == '6' || count($figures) > '4' ) 
		{
			$classes[] = 'col-4';
			$classes[] = 'col-md-2';
		}
		elseif ( count($figures) == '4' || count($figures) > '2' )
		{
			$classes[] = 'col-6';
			$classes[] = 'col-md-3';
		}
		$classlist  = implode( ' ', $classes );

		$element = '<h2>' . $title . '</h2>' . "\n";
		$element .= '<div class="row getSponsors">' . "\n";
		
		foreach($figures as $n) 
		{
			$a = $n->getElementsByTagName('a')[0];
			$img = $n->getElementsByTagName('img')[0];
			$img_url = $img->getAttribute('src');
			$img_alt = $img->getAttribute('alt');	
			
			$element .= '<div class="'. $classlist .'">' . "\n";			
			if($a)
			{
				$a_href = $a->getAttribute('href');
				$a_target = $a->getAttribute('target');
				$link = '<a href="'. $a_href.'" target="'. $a_target .'">';			
				$element .= $link . '<img class="img-fluid my-auto mx-auto d-flex" src="'. $img_url .'" alt="'. $img_alt .'" />' . '</a>' . "\n";
			}
			else
			{
				$element .= '<img class="img-fluid my-auto mx-auto d-flex" src="'. $img_url .'" alt="'. $img_alt .'" />' . "\n";
			}
			$element .= '</div>' . "\n";			
		}
		$element .= '</div>' . "\n";
		return $element;
	}
}
add_shortcode( 'getSponsors', 'getSponsors_func' );

/**
 * Disable Default Shows Archive
 */
function disable_radio_shows_archive_slug() {
    global $wp_post_types;
    if ( defined( 'RADIO_STATION_SHOW_SLUG' ) && isset( $wp_post_types[RADIO_STATION_SHOW_SLUG] ) ) {
        $post_type_object = $wp_post_types[RADIO_STATION_SHOW_SLUG];
        $post_type_object->remove_rewrite_rules();
        $post_type_object->has_archive = false;
        $post_type_object->add_rewrite_rules();
    }
}
add_action( 'init', 'disable_radio_shows_archive_slug', 11 );

/**
 * User Profile Custom Fields
 */
function add_custom_userprofile_fields( $user ) {    ?>
<table class="form-table">
<tr>
<th>
	<label for="rdp_role"><?php _e('Station Role', 'your_textdomain'); ?>
</label></th>
<td>
	<input type="text" name="rdp_role" id="rdp_role" value="<?php echo esc_attr( get_the_author_meta( 'rdp_role', $user->ID ) ); ?>" class="regular-text" /><br />
	<span class="description"><?php _e('Please enter your Radio DePaul Position.', 'your_textdomain'); ?></span>
</td>
</tr>
<tr>
</table>
<?php }
	function save_custom_userprofile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
	return FALSE;
	update_usermeta( $user_id, 'rdp_role', $_POST['rdp_role'] );
}
add_action( 'show_user_profile', 'add_custom_userprofile_fields' );
add_action( 'edit_user_profile', 'add_custom_userprofile_fields' );
add_action( 'personal_options_update', 'save_custom_userprofile_fields' );
add_action( 'edit_user_profile_update', 'save_custom_userprofile_fields' );

/**
 * Bootstrap YouTube Responsive
 */
function bootstrap_wrap_oembed( $html ){
  $html = preg_replace( '/(width|height)="\d*"\s/', "", $html ); // Strip width and height #1
  return'<div class="embed-responsive embed-responsive-16by9">'.$html.'</div>'; // Wrap in div element and return #3 and #4
}
add_filter( 'embed_oembed_html','bootstrap_wrap_oembed',10,1);