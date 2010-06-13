<html>
<head>
<title>TV @Olin</title>
<style type="text/css">
dt {
	margin: 1em 0 0.25em 0;
	font-weight: bold;
}
em, em a { color: #999; }
</style>
</head>
<body>

<!-- <h2>Show-wise listing of files in <a href="torrents/">torrents/</a>:</h2>
<em>See the <a href="index.php.txt">PHP source code</a> of this page</em><br /><br />
-->

<h2>TV @Olin</h2>

<?php

function daysAgo($timestamp){
	if($timestamp===false){ return false; }
	$s = time()-$timestamp;
    if($s<5){ return "Moments ago"; }
    if($s<60){ return $s." seconds ago"; }
    $m = $s/60; //minutes elapsed
    if($m==1){ return "1 minute ago"; }
    if($m<60){ return round($m)." minutes ago"; }
    $h = $m/60; //hours elapsed
    if($h==1){ return "1 hour ago"; }
    if($h<24){ return round($h)." hours ago"; }
    $d = $h/24; //days elapsed
    if($d==1){ return "Yesterday"; }
    if($d<7){ return round($d)." days ago"; }
    $w = $d/24; //weeks elapsed
    if($w==1){ return "Last week"; }
    return (round($w*2)/2)." weeks ago";
    }


$files = glob("torrents/*.torrent");

$pattern = '/torrents\/(.+?)( - (\d{4}-\d{2}-\d{2}), (\d{1,2}-\d{2} [AP]M) - (.+?))?(\.[^.]+)?\.torrent/';

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
	$freplaces = array( 'files/', '' );
	$recording = array(
		'show' => $show,
		'date' => $date,
		'title' => $title,
		'torrent' => $file,
		'source' => @preg_replace($fpatterns, $freplaces, $file),
		'extension' => @$matches[6]
		);
	$recordings[] = $recording;
	}
/** Group all recordings by show */
sort($recordings);
$shows = array();
foreach($recordings as $recording){
	$show = $recording['show'];
	if(!isset($shows[$show])){
		$shows[$show] = array();
		}
	$shows[$show][] = $recording;
	}
unset($recordings);

/** Print out formatted text */
echo "<dl>";
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
		if($extension){ $info[] = "$extension format"; }

		if($info){ $info = " (".implode(", ",$info).")"; }

		echo "<dd><a href=\"$recording[torrent]\">$title</a>$info</dd>";
		echo "Filesize: " . number_format(filesize($recording[source])/1024/1024/1024,2) . "GB";
		}
	}
?>
</body>
</html>

