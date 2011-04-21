<?php 
    // OPTIONS HERE.... ooooOooo so many options it's overwhelming, how are you ever going to get through all of these!?
    $yml = @'<serverdir>\plugins\WorldGuard\worlds\<worldname>\regions.yml';  //regions.yml
    $color_chestdeny = "#880000"; // what color should the region be when the flag chestaccess-deny is found?
    $color_normal = "#FFAA00"; // what color should normal regions be colored as?
    $debug = false; // will break overviewer, but show the contents of the arrays... debuging only.
    
    //Dragons below! Don't change below unless your name is Michael Writhe... :)
    $output = "// Exported from WorldGuard v5x Regions file\n";
    $output .= "// Coded by Michael Writhe - michael[at]writhem[dot]com\n";
    $output .= "// designed orignially for use on http://minecraft.writhem.com/\n";
    $output .= "var regionData=[\n";
    
    require_once "spyc.php"; // YAML Library.
    $data = spyc_load_file($yml); 
    
    $lines = file($yml);
    foreach ($data["regions"] as $label => $region) {
               
        $minx = $region["min"]["x"];
        $miny = $region["min"]["y"];
        $minz = $region["min"]["z"];
        $maxx = $region["max"]["x"];
        $maxy = $region["max"]["y"];
        $maxz = $region["max"]["z"];
        
        //determine if drawing a cube is worth our time, 2 extra polygons for cube rendering.
        // if the bottom is more than 3m lower than the top, cube is worth it.
        $cube = false;
        if(abs($miny-$minx)>3) {
            $cube = true;
        }
        
        $color = $color_normal;
        if(isset($region["flags"]["chest-access"])) {
            if ($region["flags"]["chest-access"] == "deny") {
                $color = $color_chestdeny; // no chest access = red
            }
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
        
        if($debug) { print($name);print_r($region); }
    }	
  
    $output .= "];";
    
    print $output;
 
?>