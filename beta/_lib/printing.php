<?php

/** Print the GBs */
function printGB($bytes){
	return @number_format($bytes/1024/1024/1024,1);
	}

function daysAgo($timestamp){
    if($timestamp===false){ return false; }
    $s = time()-$timestamp;
    //if($s<5){ return "moments ago"; }
    if($s<60){ return $s." sec"; }
    $m = $s/60; //minutes elapsed
    //if(round($m)<=1){ return "1 minute ago"; }
    if($m<60){ return round($m)." min"; }
    $h = $m/60; //hours elapsed
    //if(round($h)<=1){ return "1 hour ago"; }
    if($h<24){ return round($h)." hr"; }
    $d = $h/24; //days elapsed
    //if(round($d)<=1){ return "1 d"; }
	if($d<7){ return round($d).'d'; }
    $w = floor($d/7); //weeks elapsed
	$d = round($d - $w*7); //days in final partial week
    if($d==0){ return $w.'w'; }
	return $w.'w'.$d.'d';
    }

?>