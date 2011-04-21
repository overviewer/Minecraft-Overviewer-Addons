<?php
// hope you enjoy a lack of comments, cause i didn't comment this one very well at all. Pretty simple file though.
// Michael Writhe <michael [at] writhem [dot] com>
// requires the nbt class which can be downloaded from frozenfire's svn server:
// http://svn.thefrozenfire.com/minecraft/NBT/trunk/ 

require("nbt.class.php");

$nbt = new nbt();

// change to point to your world's level.dat file
$nbt->loadFile("<serverDIR>/level.dat");

$a=array();
foreach($nbt->root[0]['value'][0]['value'] as $dat) {
	$t = $dat['value'];
    
    if ($dat['name'] === "raining") { 
        $a['raining'] = $dat['value'] == 0 ? false : true;
    }
    
    if ($dat['name'] === "rainTime") { 
        $a['rainTime'] = $dat['value'];
    }
    
    if ($dat['name'] === "thundering") { 
        $a['thundering'] = $dat['value'] == 0 ? false : true;
    }
    
    if ($dat['name'] === "thunderTime") { 
        $a['thunderTime'] = $dat['value'];
    }
    
    if ($dat['name'] === 'Time') {
        $a['time'] = abs((float)$dat['value'] % 24000) ;
    }
    /*
    if ($dat['name'] === 'version') {
        $a['debug'] = $dat['value'];
    }
    */
    //print_r($dat); 
}

echo json_encode($a);

?>
