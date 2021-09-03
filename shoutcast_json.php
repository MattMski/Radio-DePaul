<?php
	// Get file content from streaming server and decode output
	$currentsong = file_get_contents('http://168.235.74.42:8000/currentsong?sid=1');
	$nextsong = file_get_contents('http://168.235.74.42:8000/nextsong?sid=1');
	$mmradio = "https://mattmski.net/images/mmRadio_square.jpg";
	$js = "The 80s Show with Jeff Stevens";
	$jsa = "https://mattmski.net/images/80sshow.png";
	$played = json_decode(file_get_contents("http://168.235.74.42:8000/played?type=json"),true);

	//Split output into array, urlencode and separate them by Artist & Title
	$info = explode(' - ', $currentsong);
	$artist = trim($info[0]);
	$title = trim($info[1]);
	
	//Search iTunes with PHP.
	function search($searchTerm){
		
		//Construct our API / web services lookup URL.
		$url = 'https://itunes.apple.com/search?term=' . urlencode($searchTerm) . '&limit=4&media=music';
		
		//Use file_get_contents to get the contents of the URL.
		$result = file_get_contents($url);
		
		//If results are returned.
		if($result !== false){
			//Decode the JSON result into an associative array and return.
			return json_decode($result, true);
		}							
	}
	 
	//Use our custom function to search when necessary
	if($title == "Advert:" && $artist == "Advert:")
	{
		$artwork = $mmradio;
	}
	else if($info[0] == $js)
	{
		$artist = trim("");
		$title = trim($currentsong);
		$artwork = $jsa;
	}
	else
	{
		//Search for the cover art
		$searchResults = search($artist . " " . $title);						
		//Loop through the search results.
		foreach($searchResults['results'] as $result)
		{
			if(($result['artistName'] == $artist) || (strpos($result['artistName'], $artist) !== false))
			{
				//Set cover art to the first album artwork / single cover.
				if(isset($result['artworkUrl100']))
				{
					$CoverArt = str_replace('100x100bb', '512x512bb', trim($result['artworkUrl100']));
				}
				break;
			}
			//Set cover art to the mmRadio logo if no artist result.
			else if($result['artistName'] != $artist)
			{
				$CoverArt = $mmradio;
				continue;
			}
		}
		
		//Set cover art to the final result from search		
		if($searchResults['resultCount'] != 0)
		{
			$artwork = $CoverArt;
		}
		//Set cover art to the mmRadio logo if no results.
		else if($searchResults['resultCount'] == 0)
		{
			$artwork = $mmradio;
		}
	}
	//Current Next Artwork Array
	$info = array("current"=>$currentsong, "next"=>$nextsong, "artwork"=>$artwork);		
	//Songs Played Array
	$songsplayed = array();
	foreach($played as $obj)
	{
		$playedat = date("F j, Y, g:i a", $obj['playedat']);
		$song = $obj['title'];
		$values = array(
		'playedat' => $playedat,
		'playedsong' => $song,
		);
		array_push($songsplayed, $values); 
	};		
	//Print out the information 
	$json = json_encode($info + $songsplayed);
	echo $json;
	//file_put_contents("/home/Matt/www/inc/shoutcast.json", $json);  
?>