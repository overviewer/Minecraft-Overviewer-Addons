<?php
    $yml = @'<serverdir>\plugins\WorldGuard\worlds\<worldname>\regions.yml'; 
    $yml = @'regions.yml'; 
    $markersFile = @'../markers.js';
    $color_chestdeny = "#880000"; // what color should the region be when the flag chestaccess-deny is found?
    $color_normal = "#FFAA00"; // what color should normal regions be colored as?
    $backFaceCull = false; // render the wireframe faces of polygons that do not point towards the camera.
    $debug = false; // will break overviewer, but show the contents of the arrays... debuging only.
    
    require_once "spyc.php"; // YAML Library.
    require_once "classes.php"; // Classes Library
    
    $data = spyc_load_file($yml); 
    
    $dr = new DetailedRegion();
    #$r = new Region();
    
    print $dr->GetType();
?>