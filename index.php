<html>
<head>
<title>TV @Olin</title>
<style type="text/css">
body {
	background-color: white;
	background-image: url('images/bg.jpg');
	background-repeat: no-repeat;
	background-position: bottom right;
}
dt {
    margin: 1em 0 0.25em 0;
    font-weight: bold;
}
a#rsslink {
	color: #e74;
	text-decoration: none;
	padding: 5px;
}

</style>
</head>
<body>


<?php

function daysAgo($timestamp){
    if($timestamp===false){ return false; }
    $s = time()-$timestamp;
    if($s<5){ return "moments ago"; }
    if($s<60){ return $s." seconds ago"; }
    $m = $s/60; //minutes elapsed
    if(round($m)<=1){ return "1 minute ago"; }
    if($m<60){ return round($m)." minutes ago"; }
    $h = $m/60; //hours elapsed
    if(round($h)<=1){ return "1 hour ago"; }
    if($h<24){ return round($h)." hours ago"; }
    $d = $h/24; //days elapsed
    if(round($d)<=1){ return "yesterday"; }
    if($d<11){ return round($d)." days ago"; }
    $w = $d/7; //weeks elapsed
    if($w<=1){ return "last week"; }
    return (round($w*2)/2)." weeks ago";
    }

/** Print the GBs */
function printGB($bytes){
	return @number_format($bytes/1024/1024/1024,2);
	}

$files = glob("torrents/*.torrent");

$pattern = '/torrents\/(.+?)( - (\d{4}-\d{2}-\d{2}), (\d{1,2}-\d{2} [AP]M) - (.+?))?(\.[^.]+)?\.torrent/';

$numRecordings = 0;
$totalBytes = 0;

/** List all shows */
$recordings = array();
foreach($files as $file){
    $matches = array();
    $matched = preg_match($pattern, $file, $matches);
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

	$fpatterns = array( '/^torrents\//', '/\.torrent$/' );
	$freplaces = array( 'recordings/', '' );
	$source = @preg_replace($fpatterns, $freplaces, $file);
	$size = @filesize($source);
    $recording = array(
        'show' => $show,
        'date' => $date,
        'title' => $title,
        'torrent' => $file,
        'source' => $source,
		'size' => $size,
        'extension' => @$matches[6]
        );
	$recordings[] = $recording;
	$numRecordings++;
	$totalBytes += $size;
	}

/** Sort recordings by show, then date (first 2 indices in array above) */
sort($recordings);

/** Group all recordings by show */
$shows = array();
foreach($recordings as $recording){
    $show = $recording['show'];
    if(!isset($shows[$show])){
        $shows[$show] = array();
        }
    $shows[$show][] = $recording;
    }
unset($recordings);
$numShows = count($shows);


?>

<h2>TV @Olin</h2>
<p><em><?php print "Now serving up $numRecordings episodes of $numShows shows totaling ".printGB($totalBytes)."GB"; ?></em></p>
<p>There's also an <a id="rsslink" href="rss.php"><img src="images/feed28.png" width="28" height="28" border="0" align="absmiddle"> RSS Feed</a> for BitTorrent clients</p>
<dl>

<?php
/** Print out formatted text */
foreach($shows as $show=>$recordings){
    echo "<dt>$show</dt>";
    foreach($recordings as $recording){
        $title = $recording['title'];
        if(!$title){ $title = $show; }
        $age = daysAgo($recording['date']);

        $info = array();

        $extension = strtolower(@$recording['extension']);
        if(@substr($extension,0,1)=="."){
            $extension = @substr($extension,1);
            }

        if($age){ $info[] = $age; }
		$info[] = date('D n/j',$recording['date']);
        $info[] = printGB($recording['size']) . "GB $extension";

        if($info){ $info = " (".implode(", ",$info).")"; }

        echo "<dd><a href=\"$recording[torrent]\">$title</a>$info</dd>";
        }
    }
?>

</dl>
</body>
</html>

