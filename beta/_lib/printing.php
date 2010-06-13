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