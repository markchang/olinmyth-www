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

/**
 * Return human readable sizes
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.3.0
 * @link        http://aidanlister.com/2004/04/human-readable-file-sizes/
 * @param       int     $size        size in bytes
 * @param       string  $max         maximum unit
 * @param       string  $system      'si' for SI, 'bi' for binary prefixes
 * @param       string  $retstring   return string format
 */
function size_readable($size, $max = null, $system = 'si', $retstring = '%01.2f %s')
{
    // Pick units
    $systems['si']['prefix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
    $systems['si']['size']   = 1000;
    $systems['bi']['prefix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
    $systems['bi']['size']   = 1024;
    $sys = isset($systems[$system]) ? $systems[$system] : $systems['si'];

    // Max unit to display
    $depth = count($sys['prefix']) - 1;
    if ($max && false !== $d = array_search($max, $sys['prefix'])) {
        $depth = $d;
    }

    // Loop
    $i = 0;
    while ($size >= $sys['size'] && $i < $depth) {
        $size /= $sys['size'];
        $i++;
    }

    return sprintf($retstring, $size, $sys['prefix'][$i]);
}

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
	$size = exec ('stat -L -c %s ' . escapeshellarg ($source) );
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

<h2>TV @Olin (compressed)</h2>
<p>These are all the <b>compressed</b> shows we have. <br />
<a href="http://mythtv.olin.edu/">Raw recordings</a> are available. They are fresher, but expire faster.</p>
<p><em><?php print "Now serving up $numRecordings episodes of $numShows shows totaling ".size_readable($totalBytes); ?></em></p>
<p>There's also an <a id="rsslink" href="rss.php"><img src="images/feed28.png" width="28" height="28" border="0" align="absmiddle"> RSS Feed</a> for BitTorrent clients, and a <a href="/beta">beta</a> interface you can try.</p>
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
        //$info[] = printGB($recording['size']) . "GB $extension";
        $info[] = size_readable($recording['size']) . " $extension";
        if($info){ $info = " (".implode(", ",$info).")"; }

        echo "<dd><a href=\"$recording[torrent]\">$title</a>$info</dd>";
        }
    }
?>

</dl>
</body>
</html>

