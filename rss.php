<rss version="2.0">
	<channel>
		<title>TV @Olin</title>
		<ttl>15</ttl>
		<link>http://tv.olin.edu/rss.php</link>
		<description>The latest recorded TV shows on TV@Olin.</description>

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
	$source = @preg_replace($fpatterns, $freplaces, $file);
    $recording = array(
        'show' => $show,
        'date' => $date,
        'title' => $title,
        'torrent' => $file,
        'source' => $source,
        'size' => @filesize($source),
        'extension' => @$matches[6]
        );
	$recordings[] = $recording;
	}

/** Sort recordings by date ascending */
function showCmp($a, $b){ return $b['date'] - $a['date']; }
usort($recordings, 'showCmp');

/** Output */
foreach($recordings as $rec){
	$date = date('Y m/d D',$rec['date']);
?>
		<item> 
			<title><![CDATA[<?php print "$rec[show] - $date - $rec[title]"; ?>]]></title> 
			<link><?php print urlencode($rec['torrent']); ?></link> 
			<category domain="http://tv.olin.edu/?show=<?php urlencode($rec['show']); ?>"><![CDATA[TV Show / <?php htmlspecialchars($rec['show']); ?>]]></category> 
			<pubDate><?php print date(DATE_RSS,$rec['date']); ?></pubDate> 
			<description><![CDATA[Show Name: <?php htmlspecialchars($rec['show']); ?>; Episode Title: <?php htmlspecialchars($rec['title']); ?>]]></description> 
			<enclosure url="http://tv.olin.edu/<?php print htmlspecialchars($rec['torrent']);?>" length="<?php print $rec['size']; ?>" type="application/x-bittorrent" /> 
			<guid>http://tv.olin.edu/<?php print htmlspecialchars($rec['torrent']);?></guid> 
		</item> 
<?php
	
	
}

?>
	</channel> 
</rss> 

