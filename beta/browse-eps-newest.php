
<html>
<head>
<title>TV @Olin</title>
<link rel="stylesheet" type="text/css" href="_css/main.css" />
</head>
<body>

<?php

require_once('_lib/printing.php');
require_once('_lib/parsing.php');

$files = glob("../torrents/*.torrent");

$numRecordings = 0;
$totalBytes = 0;

/** List all recordings */
$recordings = array();
foreach($files as $file){
	$recording = parseFilename($file);
	$recordings[] = $recording;
	$totalBytes += $recording['size'];
	}
$numRecordings = count($recordings);

/** Sort recordings by show, then date (first 2 indices in array above) */
rsort($recordings);

/** Group all recordings by show */
$shows = groupRecordings($recordings);
$numShows = count($shows);

?>

<div class="MainWrapper">
<div class="MenuBar">
Shows:
	<a href="browse-show.php">Alphabetical</a>&nbsp;&nbsp|&nbsp;&nbsp;
Episodes:
	<a href="browse-eps-show.php">Shows</a> or 
	<a href="browse-eps-newest.php" class="Current">Newest</a>
</div>

<h2>TV @Olin</h2>
<p><em><?php print "Now serving up $numRecordings episodes of $numShows shows totaling ".printGB($totalBytes)."GB"; ?></em></p>
<p>There's also an <a id="rsslink" href="rss.php"><img src="images/feed28.png" width="28" height="28" border="0" align="absmiddle"> RSS Feed</a> for BitTorrent clients</p>
<hr size="1" /><br />
<?php
	$mostRecent = array(10,25,50);
	?>
<div id="search">Showing the latest &nbsp;[
<?php
	$maxResults = @$_REQUEST['n'];
	if(!$maxResults){ $maxResults = 25; }
	$options = array();
	foreach($mostRecent as $val){
		if($maxResults==$val){
			$options[] = $val;
		}else{
			$options[] = "<a href=\"browse-eps-newest.php?n=$val\">$val</a>";
			}
		}
	echo implode(" | ", $options);
	?> ]&nbsp; recorded episodes.
<br /><br />
</div>
<hr size="1" /><br />

<div class="AllEpisodes">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<thead><tr>
		<th style="width:20px;">&nbsp;</th>
		<th>Show</th>
		<th>Episode</th>
		<th>Date</th>
		<th>Age</th>
		<th>Size</th>
		</tr></thead>
	<tbody>
	<?php
	$shaded = true;
	$count = 0;
    foreach($recordings as $recording){
		if($count>$maxResults){ break; }
		$count++;
		
		$shaded = !$shaded;
		
		$showName = $recording['show'];
        $title = $recording['title'];
        if(!$title){ $title = $showName; }
        $age = daysAgo($recording['date']);

        $info = array();

        $extension = strtolower(@$recording['extension']);
        if(@substr($extension,0,1)=="."){
            $extension = @substr($extension,1);
            }

        if(!$age){ $age=""; }
		$airdate = date('n/j',$recording['date']);
        $size = printGB($recording['size']) . "G";
		$format = $extension;

        if($info){ $info = " (".implode(", ",$info).")"; }
		
		$tableCols = array("<a href=\"$recording[torrent]\"><img src=\"images/download.png\" width=16 height=16 border=0></a>",$showName,$title,$airdate,$age,$size);
		$shading = $shaded ? ' class="Shaded"' : '';
		
		echo "<tr$shading><td>".implode("</td><td>",$tableCols)."</td></tr>\n";
		
        }
	?>
	</tbody></table>

</div>

</div>
</body>
</html>

