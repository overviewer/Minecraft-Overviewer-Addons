<?php
// CONFIG 

$skinurl = 'http://s3.amazonaws.com/MinecraftSkins/'; //Where to download the full skins.

$cachelifetime = 5; // Set how long time (in days) the face will be loaded from a cached copy before updating from minecraft.net. Set to 0 if you do not want this feature.

$cachepath = '/tmp/mccache/'; // Where to store cached copies.
$timeout = 2; // Timeout for fetching a skin png from minecraft.net. Needed if their servers die.

// END CONFIG 


if (isset($_GET['clearcache'])) 
{
	$mask = $cachepath.'*.png';
   	array_map( 'unlink', glob( $mask ) );
   	echo 'Cache clear: DONE';   	
   	exit();
}
header('Content-type: image/png');


function CreateBlankPNG($w, $h)
{
    $im = imagecreatetruecolor($w, $h);
    imagesavealpha($im, true);
    $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
    imagefill($im, 0, 0, $transparent);
    return $im;
}
$fromcache = false;
function LoadPlayerImage($nick,$usage)
{
    global $cachepath, $skinurl, $cachelifetime, $cachepath, $timeout,$fromcache;
    $im = false;  
	$cachedpng = $cachepath.$nick.'_'.$usage.'.png';
    
    if (!is_dir($cachepath)) { mkdir($cachepath); }
    // cache the part
    if (file_exists($cachedpng) && filemtime($cachedpng) >= (time() - ($cachelifetime * 24 * 60 * 60))) 
    {     
        $im = imagecreatefrompng($cachedpng);
        $fromcache = true;
    }
    else
    { 
        $cachedpng = $cachepath.$nick.'.png'; 
        $skinurl = $skinurl.$nick.'.png';            
        $old = ini_set('default_socket_timeout', $timeout);
        if ($file = fopen($skinurl, "r")) 
        {
            // Attempt to open 
            if($cachelifetime != 0)
                $im = imagecreatefrompng($skinurl);
        }     
    }
    // See if it failed 
    if(!$im)
    {   
        $im = imagecreatefrompng('./char.png');
    }
    
    return $im;
}

$player = @$_GET['player'];
$usage  = @$_GET['usage'];

$img = LoadPlayerImage($player,$usage);

if($usage == 'list')
{
	$myhead = imagecreate(15, 15);
	$color = imagecolorallocate($myhead, 250, 250, 250);
	imagefill($myhead, 0, 0, $color);

	imagecopyresized($myhead, $img, 1, 1, 8, 8, 13, 13, 8, 8);    
}
if($usage == 'marker')
{
  $myhead = CreateBlankPNG(21, 27);
  $mymarker = imagecreatefrompng('./marker_empty.png');
  
  imagecopy($myhead, $mymarker, 0, 0, 0, 0, 21, 27);
  imagecopyresized($myhead, $img, 1, 1, 8, 8, 19, 20, 8, 8);
}
if($usage == 'info')
{
	$myhead = CreateBlankPNG(48, 96);
	
	imagecopyresized($myhead, $img, 12,0,8,8,24,24,8,8);
	imagecopyresized($myhead, $img, 12,24,20,20,24,26,8,12);
	imagecopyresized($myhead, $img, 12,50,0,20,12,26,4,12);
	imagecopyresized($myhead, $img, 24,50,8,20,12,26,4,12);
	imagecopyresized($myhead, $img, 2,24,44,20,10,26,4,12);
	imagecopyresized($myhead, $img, 36,24,52,20,10,26,4,12);

	imagecopyresized($myhead, $img, 6,6,32,10,6,3,2,1);
	imagecopyresized($myhead, $img, 36,6,32,10,6,3,2,1);
}

// add to cache
if($cachelifetime != 0)
{   
    $cachedpng = $cachepath.$player.'_'.$usage.'.png';
    if (!$fromcache)
    {
        imagepng($myhead, $cachedpng,9);
    }      
    header('Cache-Control: max-age=' .($cachelifetime*86400)); // set the cache lifetime in days as seconds.
    header('Last-Modified:'.gmdate("D, d M Y H:i:s",filemtime($cachedpng)) . " GMT");
    header('Content-Length: ' . filesize($cachedpng));
    
    readfile($cachedpng);
}
else
{
    imagepng($myhead);
}
imagedestroy($img);
imagedestroy($myhead);
?>

