/* 
 * Template Name: Radio DePaul Custom JavaScript
 * Author : Matthew Mola
 * Date: 3/7/2021
 * Version 1.2
*/ 

//Define Variables
var title, artist, album, artwork, rdptext, rdp = "";
var urlCoverArt, urlCoverArt96, urlCoverArt128, urlCoverArt192, urlCoverArt256, urlCoverArt384;
rdptext = "Radio DePaul";
//Set the reloading
document.onload =  getStreamingData();

window.onload = function() {
  document.getElementById('player_desk_title').style.display = 'none';
};

window.onload = function () {   

	getStreamingData();
	// Interval to get streaming data in miliseconds
	setInterval(function () {
		getStreamingData();
	}, 5000);

}

function togglePlay() {
  var audio = document.getElementsByTagName("audio")[0];
   
  if (audio) {
    if (audio.paused) {
        audio.play();
		document.getElementById("player_desk_button").className = "fas fa-pause-circle fa-3x";
		document.getElementById("player_mob_button").className = "fas fa-pause-circle fa-3x";
    } else {
        audio.pause();
        document.getElementById("player_desk_button").className = "fas fa-play-circle fa-3x";
		document.getElementById("player_mob_button").className = "fas fa-play-circle fa-3x";
    }
  }
}

function getStreamingData() 
{	
	//Get the rest of the data
	fetch("https://radiodepaul.com/json/radiodepaul.json").then((response) => { return response.json() }).then((data) => 
	{
		//Work with JSON data here
		rdp = JSON.parse(JSON.stringify(data));
		artist = rdp.artist;
		title = rdp.song;
		artwork = rdp.artwork;
		album = rdptext;
		//title =  "The Poor Fisherman (LP Version-1972)";
		//artist = "Yusef Lateef";
		//Main Title and The Attack on the Jakku Village
		//John Williams
		//My Brain Is Hanging Upside Down (Bonzo Goes To Bitburg)
		//Ramones
		//title =  "In This Temple As In The Hearts Of Man For Whom He Saved The Earth";
		//title = "Damita Menezes";
		//artist = "Damita Menezes Damita";

		//Set Current Song and Artwork
		title = title.replace(/ \([\s\S]*?\)/g, '');
		document.getElementById("player_desk_title").innerHTML = title;
		document.getElementById("player_desk_artist").innerHTML = artist;
		document.getElementById("player_mob_title").innerHTML = title;
		document.getElementById("player_mob_artist").innerHTML = artist;
		document.getElementById("player_desk_artwork").src = artwork;
		document.getElementById("player_mob_artwork").src = artwork;
		document.getElementsByClassName("player_desk")[0].style.display = "block";		
		
		//Cover Art Resizing for Media Session
		urlCoverArt = (artwork != urlCoverArt) ? artwork.replace('170x170bb', '512x512bb') : urlCoverArt;
		urlCoverArt96 = (artwork != urlCoverArt) ? urlCoverArt.replace('512x512bb', '96x96bb') : urlCoverArt;
		urlCoverArt128 = (artwork != urlCoverArt) ? urlCoverArt.replace('512x512bb', '128x128bb') : urlCoverArt;
		urlCoverArt192 = (artwork != urlCoverArt) ? urlCoverArt.replace('512x512bb', '192x192bb') : urlCoverArt;
		urlCoverArt256 = (artwork != urlCoverArt) ? urlCoverArt.replace('512x512bb', '256x256bb') : urlCoverArt;
		urlCoverArt384 = (artwork != urlCoverArt) ? urlCoverArt.replace('512x512bb', '384x384bb') : urlCoverArt;
		
		//Set Cover Art to Cover if cover.jpg is detected
		if(artwork.includes("cover.jpg"))
		{
			urlCoverArt = artwork;
		}
		
		//Final Media Session
		if ('mediaSession' in navigator) {
			navigator.mediaSession.metadata = new MediaMetadata({
				title: title,
				artist: artist,
				album: album,
				artwork: [{
						src: urlCoverArt96,
						sizes: '96x96',
						type: 'image/png'
					},
					{
						src: urlCoverArt128,
						sizes: '128x128',
						type: 'image/png'
					},
					{
						src: urlCoverArt192,
						sizes: '192x192',
						type: 'image/png'
					},
					{
						src: urlCoverArt256,
						sizes: '256x256',
						type: 'image/png'
					},
					{
						src: urlCoverArt384,
						sizes: '384x384',
						type: 'image/png'
					},
					{
						src: urlCoverArt,
						sizes: '512x512',
						type: 'image/png'
					}
				]
			});
		}
	})
}

jQuery(document).ready(function($)
{
	//Set Button text to dropdown item text when clicked
	$(".dropdown-menu a").click(function () {
		var selText = $(this).text();
		$(".dropdown-toggle").html(selText);
	});

	//Set Button class active when clicked
	$(".btn").click(function () 
	{
		$(".btn").removeClass("active");
		$(this).addClass("active");        
	});
	//Set Dropdown class active when clicked
	$(".dropdown-item").click(function () 
	{
		$(".dropdown-item").removeClass("active");
		$(this).addClass("active");        
	});
	
	//Load More Script for News
	
	//Hides All Posts by default
	$("#all-posts div.post-row").hide();
	//Shows the first two by default
	$("#all-posts div.post-row").slice(0, 5).show().addClass("d-flex");
	//Button adds two more posts
	$(".all-load-more").click(function(){
		var showing = $("#all-posts .post-row:visible").length;
		$("#all-posts .post-row").slice(showing - 1, showing + 5).show().addClass("d-flex");
	});
	
	//Hides Entertainment Posts by default
	$("#entertainment-posts div.post-row").hide();
	//Shows the first two by default
	$("#entertainment-posts div.post-row").slice(0, 5).show().addClass("d-flex");
	//Button adds two more posts
	$(".entertainment-load-more").click(function(){
		var showing = $("#entertainment-posts .post-row:visible").length;
		$("#entertainment-posts .post-row").slice(showing - 1, showing + 5).show().addClass("d-flex");
	});
	
	//Hides News Posts by default
	$("#news-posts div.post-row").hide();
	//Shows the first two by default
	$("#news-posts div.post-row").slice(0, 5).show().addClass("d-flex");
	//Button adds two more posts
	$(".news-load-more").click(function(){
		var showing = $("#news-posts .post-row:visible").length;
		$("#news-posts .post-row").slice(showing - 1, showing + 5).show().addClass("d-flex");
	});
	
	//Hides Podcasts Posts by default
	$("#podcasts-posts div.post-row").hide();
	//Shows the first two by default
	$("#podcasts-posts div.post-row").slice(0, 5).show().addClass("d-flex");
	//Button adds two more posts
	$(".podcasts-load-more").click(function(){
		var showing = $("#podcasts-posts .post-row:visible").length;
		$("#podcasts-posts .post-row").slice(showing - 1, showing + 5).show().addClass("d-flex");
	});
	
	//Hides Newsletter Posts by default
	$("#newsletter-posts div.post-row").hide();
	//Shows the first two by default
	$("#newsletter-posts div.post-row").slice(0, 5).show().addClass("d-flex");
	//Button adds two more posts
	$(".newsletter-load-more").click(function(){
		var showing = $("#newsletter-posts .post-row:visible").length;
		$("#newsletter-posts .post-row").slice(showing - 1, showing + 5).show().addClass("d-flex");
	});
});
