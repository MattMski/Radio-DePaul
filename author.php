<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */

get_header();
global $query_string;
query_posts( $query_string . '&posts_per_page=-1' );
$shows = radio_station_get_shows();
?>

	<section id="primary" class="author content-area container">
		<div id="main" class="site-main" role="main">

		<?php
		if ( have_posts() )
		{
			$user_info = get_userdata( $author );
			$user_name = $user_info->display_name;
			$user_ID = $user_info->ID;
			$avatar = get_wpupa_url($user_ID, ['size' => 'medium']);
			//$user_avatar = '<div class="img-fluid">' . get_avatar($user_ID, '500', $default, $alt, array( 'class' => array( 'd-block', 'mx-auto' ) ))  . '</div>' . "\n";
			$user_avatar = '<div class="show-host-avatar"><img class="img-fluid" alt="' . $user_name . "'s Picture" . '" src="' . $avatar . '"></div>' . "\n";	
			$user_meta = get_user_meta( $author );
			$user_desc = $user_meta['description'][0];
			$station_position = trim(get_the_author_meta('rdp_role', $author));
			$show_section = "";
			foreach ($shows as $show)
			{
				//echo $author;
				$show_id = $show->ID;
				//var_dump($show_id);
				$hosts = get_post_meta( $show_id, 'show_user_list', true );
				$show_name = get_the_title( $show_id );
				$show_avatar = radio_station_get_show_avatar( $show_id, $avatar_size, ["class" => "img-fluid"] );
				
				//High Resolution Avatar Fix
				if(stripos($show_avatar, "\"1\" height=\"1\"") !== false && $show_avatar)
				{
					$show_avatar = '<img src="' . radio_station_get_show_avatar_url( $show_id ) . '" class="img-fluid d-block mx-auto w-auto" alt="' . $show_name . ' Show Logo"/>';
				}
				
				//Small Image Fix
				$doc = new DOMDocument();
				$doc->loadHTML($show_avatar);
				$xpath = new DOMXPath($doc);
				$src = $xpath->evaluate("string(//img/@src)"); # "/images/image.jpg"
				if ((stripos($src, '500x500') === false) && $show_avatar)
				{
					$show_avatar = '<img src="' . $src . '" class="img-fluid d-block mx-auto w-auto" alt="' . $show_name . ' Show Logo"/>';
				}
				
				$show_link = get_permalink( $show_id );				
				
				foreach ($hosts as $host)
				{
					if($host == $author)
					{
						$show_section .= "<div class='col-xs-12 col-md-4'>" . "\n";
						$show_section .= '<a href="' . esc_url( $show_link ) . '">' . "\n";
						$show_section .= "<div class='show-avatar'>\n" . $show_avatar  . "</div>\n";  
						$show_section .= "<div class='show-name text-center py-1'><b>\n" . $show_name . "</b></div>\n";
						$show_section .= '</a>' . "\n";	
						$show_section .= "</div>\n";									
					}
				}
			}
			
			?>
			<header class="entry-header">
				<?php 
					echo '<h4 class="text-uppercase">'. $user_name . '</h1>'; 
				?>
			</header><!-- .entry-header -->
			<?php	
			
			if ( $avatar )
			{
				$string = '<div class="row">' . "\n";
				$string .= '<div class="col-md-4 col-xs-12">' . "\n";
				$string .= $user_avatar;
				$string .= '</div>' . "\n";
				$string .= '<div class="col-md-8 col-xs-12">' . "\n";
				$string .= '<p><b>' . $station_position . '</b></p>';
				$string .= '<p>' . $user_desc . '</p>';
				$string .= '</div>' . "\n";
				$string .= '</div>' . "\n";
				echo $string;
			}
			else
			{
				$string = '<div class="row">' . "\n";
				$string .= '<div class="col-md-12 col-xs-12">' . "\n";
				$string .= '<p><b>' . $station_position . '</b></p>';
				$string .= '<p>' . $user_desc . '</p>';
				$string .= '</div>' . "\n";
				$string .= '</div>' . "\n";
				echo $string;
			}
			
			if( $show_section )
			{
				$string = '<div class="row">' . "\n";
				$string .= '<div class="col-md-12 col-xs-12">' . "\n";
				$string .= '<h3>Shows</h3>' . "\n";
				$string .= '<div class="row">' . "\n";
				$string .= $show_section;
				$string .= '</div>' . "\n";
				$string .= '</div>' . "\n";
				$string .= '</div>' . "\n";
				echo $string;
			}
			
			/*$string = '<div class="row">' . "\n";
			$string .= '<div class="col-md-12 col-xs-12">' . "\n";
			$string .= '<h3>Articles</h3>' . "\n";
			$string .= '</div>' . "\n";
			$string .= '</div>' . "\n";
			echo $string;*/			
			
			?>
			<div class="row">			
				<div class="col-md-12 col-xs-12">
					<h3>Articles</h3>			
					<?php
					/* Start the Loop */
					while ( have_posts() ) : the_post();
					?>			
						<li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
					<?php
					endwhile;
					?>
				</div>				
			</div>
			<?php
		}
		?>

		</div><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();