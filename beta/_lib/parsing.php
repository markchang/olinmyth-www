<?php

/* 	Parses the filename "torrents/xyz.torrent"
	into structured data.
	Returns: array({
		'show' => String,
		'date' => date,
		'title' => String,
		'torrent' => String,
		'source' => String,
		'size' => int,
		'extension' => String
		})
	*/
function parseFilename($filename){
	$pattern = '/torrents\/(.+?)( - (\d{4}-\d{2}-\d{2}), (\d{1,2}-\d{2} [AP]M) - (.+?))?(\.[^.]+)?\.torrent/';
	$matches = array();
    $matched = preg_match($pattern, $filename, $matches);
    $show = @$matches[1];
    $time = '';
    if(@$matches[4]){
        $time = ' '.@preg_replace('/-/',':',@$matches[4]);
        }
    $date = false;
    if(@$matches[3]){
        $date = @strtotime(@$matches[3]." ".$time);
        }
    $title = @$matches[5];

	$fpatterns = array( '/^..\/torrents\//', '/\.torrent$/' );
	$freplaces = array( '../recordings/', '' );
	$source = @preg_replace($fpatterns, $freplaces, $filename);
        // have to use stat here because filesize() has overflow
        // issues and is just not robust
	$size = exec ('stat -c %s ' . escapeshellarg ($source) );
	
	//Define these keys in the sort order (show first, then date, then title, etc...)
    $recording = array(
        'date' => $date,
        'show' => $show,
        'title' => $title,
        'torrent' => $filename,
        'source' => $source,
		'size' => $size,
        'extension' => @$matches[6]
        );
	
	return $recording;
	}


/*	Takes an array of recordings and groups them into shows
	Returns array({
		'name' => String,
		'art' => String,
		'recordings' => array({
			'show' => String,
			'date' => date,
			'title' => String,
			'torrent' => String,
			'source' => String,
			'size' => int,
			'extension' => String
			})
		})
	*/
function groupRecordings($recordings){
	$shows = array();
	foreach($recordings as $recording){
		$showName = $recording['show'];
		if(!isset($shows[$showName])){
			$art = "images/shows/$showName.jpg";
			$hasArt = @file_exists($art);
			if(!$hasArt){ $art = "images/shows/Untitled.png"; }
			$shows[$showName] = array('name'=>$showName,'hasArt'=>$hasArt,'art'=>$art, 'recordings'=>array());
			}
		$shows[$showName]['recordings'][] = $recording;
		}
	return $shows;
	}

?>
