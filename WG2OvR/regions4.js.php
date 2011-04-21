<?php 
    $txt = file('<serverdir>\plugins\WorldGuard\regions.txt'); //regions.txt
    $color_chestdeny = "#880000"; // what color should the region be when the flag chestaccess-deny is found?
    $color_normal = "#FFAA00"; // what color should normal regions be colored as?
    $debug = false; // will break overviewer, but show the contents of the arrays... debuging only.

    //Dragons below! Don't change below unless your name is Michael Writhe... :)
    $output = "// Exported from WorldGuard v4x Regions file\n";
    $output .= "// Coded by Michael Writhe - michael[at]writhem[dot]com\n";
    $output .= "// designed orignially for use on http://minecraft.writhem.com/\n";
    $output .= "var regionData=[\n";
    
    $debug = false;
    foreach ($txt as $line_num => $line) {
        $values = explode('","',$line);
        
        $values[0] = substr($values[0],1);
        $values[14] = substr($values[14] ,-1,1);
        
        //determine if drawing a cube is worth our time, 2 extra polygons for cube rendering.
        $cube = false;
        if(abs($values[3]-$values[6])>3) {
            $cube = true;
        }
        
        $minx = $values[2];
        $miny = $values[3];
        $minz = $values[4];
        $maxx = $values[5];
        $maxy = $values[6];
        $maxz = $values[7];
        $label = $values[0];
        
        $color = $color_normal;
        if($values[12] == "-C") {
            $color = $color_chestdeny; // no chest access = red (default)
        }
        //center
        $output .= "   //{$label}:top layer\n";
        $output .= "   {\"label\": \"{$label}-top\", \"color\": \"{$color}\", \"opacity\": 0.5, \"closed\": true, \"path\": [\n";
        $output .= "     {\"x\": {$minx}, \"y\": {$maxy}, \"z\": {$minz}},\n";
        $output .= "     {\"x\": {$minx}, \"y\": {$maxy}, \"z\": {$maxz}},\n";
        $output .= "     {\"x\": {$maxx}, \"y\": {$maxy}, \"z\": {$maxz}},\n";
        $output .= "     {\"x\": {$maxx}, \"y\": {$maxy}, \"z\": {$minz}}\n";
        $output .= "   ]},\n";

        if($cube) {
            //isometric cube draw
            $output .= "   //{$label}:isometric projection-left\n";
            $output .= "   {\"label\": \"{$label}-isoleft\", \"color\": \"{$color}\", \"opacity\": 0.5, \"closed\": true, \"path\": [\n";
            $output .= "     {\"x\": {$minx}, \"y\": {$miny}, \"z\": {$minz}},\n";
            $output .= "     {\"x\": {$minx}, \"y\": {$miny}, \"z\": {$maxz}},\n";
            $output .= "     {\"x\": {$minx}, \"y\": {$maxy}, \"z\": {$maxz}},\n";
            $output .= "     {\"x\": {$minx}, \"y\": {$maxy}, \"z\": {$minz}}\n";
            $output .= "   ]},\n";
            
            $output .= "   //{$label}:isometric projection-right\n";
            $output .= "   {\"label\": \"{$label}-isoright\", \"color\": \"{$color}\", \"opacity\": 0.5, \"closed\": true, \"path\": [\n";
            $output .= "     {\"x\": {$maxx}, \"y\": {$maxy}, \"z\": {$maxz}},\n";
            $output .= "     {\"x\": {$minx}, \"y\": {$maxy}, \"z\": {$maxz}},\n";
            $output .= "     {\"x\": {$minx}, \"y\": {$miny}, \"z\": {$maxz}},\n";
            $output .= "     {\"x\": {$maxx}, \"y\": {$miny}, \"z\": {$maxz}}\n";
            $output .= "   ]},\n";
        }
        
        if($debug) { print_r($values); }
    }	
    
    
    $output .= "];";
    
    
    print $output;
 
?>