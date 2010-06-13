<html>
<head>
<title>TV @Olin</title>
<link rel="stylesheet" type="text/css" href="_css/main.css" />
<script src="_js/jquery.js" language="JavaScript" type="text/javascript"></script>
<script src="_js/jquery.ui.js" language="JavaScript" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">

$(document).ready(function(){
	var showNameFilter = $('#showNameFilter');
	var episodeLists = $(".AllEpisodes .EpisodeList");	
	
	//go back to the show-browsing interface
	$("#ShowAllShows").click(function(){
		$('#showNameFilter').val('').change().focus();
		return false;
		});
	$("#allShowsOption").hide();
	
	var lastQuery = null;
	
	showNameFilter.change(function(){
		var query = showNameFilter.val().toLowerCase();
		if(query==lastQuery){
			return;
			}
		lastQuery = query;
		
		var queryParts = query.split(" ");
		var numMatched = 0;
		var numNotMatched = 0;
		//check each show to see if it matches user search terms
		episodeLists.each(function(index){
			var show = $(episodeLists[index]);
			var name = show.children('h1').text().toLowerCase();
			var matched = true;
			for(partIndex in queryParts){
				var queryTerm = queryParts[partIndex];
				if(name.indexOf(queryTerm)<0){ //show name didn't have one of the query terms
					matched = false;
					break;
					}
				}
			if(matched){
				show.show();
				numMatched++;
			}else{
				show.hide();
				numNotMatched++;
				}
			});
		if(numNotMatched>0){
			$("#allShowsOption").show();
		}else{
			$("#allShowsOption").hide();
			}
		});
	showNameFilter.keyup(function(e){ showNameFilter.change(); });
	showNameFilter.focus();
	showNameFilter.change();
	});

</script>
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

sort($shows);
$numShows = count($shows);

?>

<div class="MainWrapper">
<div class="MenuBar">
Shows:
	<a href="browse-show.php">Alphabetical</a>&nbsp;&nbsp|&nbsp;&nbsp;
Episodes:
	<a href="browse-eps-show.php" class="Current">Shows</a> or 
	<a href="browse-eps-newest.php">Newest</a>
</div>

<h2>TV @Olin</h2>
<p><em><?php print "Now serving up $numRecordings episodes of $numShows shows totaling ".printGB($totalBytes)."GB"; ?></em></p>
<p>There's also an <a id="rsslink" href="rss.php"><img src="images/feed28.png" width="28" height="28" border="0" align="absmiddle"> RSS Feed</a> for BitTorrent clients</p>
<hr size="1" /><br />

<div id="Search">
Filter by show: <input type="text" id="showNameFilter" size="20" value="<?php print htmlspecialchars(@$_REQUEST['q']); ?>" />
<span id="allShowsOption">&nbsp;&nbsp;|&nbsp;&nbsp;<a id="ShowAllShows" href="#">All Shows</a></span>
<br /><br />
</div>

<div class="AllEpisodes">
<?php
/** Print out show list */
foreach($shows as $showInfo){
	$art = $showInfo['art'];
	$showName = $showInfo['name'];
	$recordings = $showInfo['recordings'];
    echo "<div class=\"EpisodeList\"><h1 class=\"ShowName\"><img align=\"absmiddle\" class=\"ShowArt\" src=\"$showInfo[art]\"> $showName</h1><br />\n\t";
	?>
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<thead><tr>
		<th style="width:20px;">&nbsp;</th>
		<th width="">Episode Title</th>
		<th width="120px">Airdate</th>
		<th width="80px">Age</th>
		<th width="80px">Size</th>
		<th width="80px">Fmt</th>
		</tr></thead>
	<tbody>
	<?php
	$shaded = true;
    foreach($recordings as $recording){
		$shaded = !$shaded;
		
        $title = $recording['title'];
        if(!$title){ $title = $showName; }
        $age = daysAgo($recording['date']);

        $info = array();

        $extension = strtolower(@$recording['extension']);
        if(@substr($extension,0,1)=="."){
            $extension = @substr($extension,1);
            }

        if(!$age){ $age=""; }
		$airdate = date('D n/j',$recording['date']);
        $size = printGB($recording['size']) . "GB";
		$format = $extension;

        if($info){ $info = " (".implode(", ",$info).")"; }
		
		$tableCols = array("<a href=\"$recording[torrent]\"><img src=\"images/download.png\" width=16 height=16 border=0></a>",$title,$airdate,$age,$size,$format);
		$shading = $shaded ? ' class="Shaded"' : '';
		
		echo "<tr$shading><td>".implode("</td><td>",$tableCols)."</td></tr>\n";
		
        }
	?>
	</tbody></table>
	<?php
	echo("</div>\n");
    }
?>
</div>

</div>
</body>
</html>

