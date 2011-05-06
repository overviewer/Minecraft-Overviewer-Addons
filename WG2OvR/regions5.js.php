<?php 
    // OPTIONS HERE.... ooooOooo so many options it's overwhelming, how are you ever going to get through all of these!?
    $yml = @'regions.yml'; 
    $color_chestdeny = "#880000"; // what color should the region be when the flag chestaccess-deny is found?
    $color_normal = "#FFAA00"; // what color should normal regions be colored as?
    $debug = false; // will break overviewer, but show the contents of the arrays... debuging only.
    
    //Dragons below! Don't change below unless your name is Michael Writhe... :)
    $output = "// Exported from WorldGuard v5x Regions file\n";
    $output .= "// Coded by Michael Writhe - michael[at]writhem[dot]com\n";
    $output .= "// http://goo.gl/dc0tV\n";
    $output .= "overviewer.collections.regionDatas.push([\n";
    
    require_once "spyc.php"; // YAML Library.
    $data = spyc_load_file($yml); 
    
    $lines = file($yml);
    foreach ($data["regions"] as $label => $region) {
        
        if ($region["type"] == "cuboid") { 
            //cube.
            $color = $color_normal;
            if(isset($region["flags"]["chest-access"])) {
                if ($region["flags"]["chest-access"] == "deny") {
                    $color = $color_chestdeny; // no chest access = red
                }
            }

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
            
            if($debug) { print($label);print_r($region); }
        } else { 
            //polygon. -------------------------------------------------------------------------------------
            $color = $color_normal;
            if(isset($region["flags"]["chest-access"])) {
                if ($region["flags"]["chest-access"] == "deny") {
                    $color = $color_chestdeny; // no chest access = red
                }
            }

            // whats the virtical limits? easy for polygons ... holy dina!
            $miny = $region["min-y"];
            $maxy = $region["max-y"];
            
            //determine if drawing a cube is worth our time, 2 extra polygons for cube rendering.
            // if the bottom is more than 3m lower than the top, cube is worth it.
            $cube = false;
            if(($maxy-$miny)>2) {
                $cube = true;
            } else {
                $maxy = round(($maxy+$miny)/2,0);
            }
            
            //extract the points from the yml data in order to work with them easier later.
            $points = array("x"=>array(),"z"=>array());
            for ($n = 0;$n <= count($region);$n++) {
                if (isset($region[$n]["x"]) && isset($region[$n]["z"])) {
                    array_push($points["x"],$region[$n]["x"]);
                    array_push($points["z"],$region[$n]["z"]);
                }
            }
            
            // generate the top layer
            $output .= "   //{$label}:top layer\n";
            $output .= "   {\"label\": \"{$label}-top\", \"color\": \"{$color}\", \"opacity\": 0.5, \"closed\": true, \"path\": [\n";
            
            for ($n = 0;$n < count($points["x"]);$n++) {
                $output .= "     {\"x\": {$points["x"][$n]}, \"y\": {$maxy}, \"z\": {$points["z"][$n]}},\n";
            }
            //same operation for every line, but last line has to be different, strip the , at the end of it.
            $output = substr($output,0,-2);
            $output .= "\n   ]},\n";

            if ($cube) { //------------------------------------
                //array_push($faces,$face);
                for ($n = 1; $n <= count($points["x"]);$n++) {
                    /*
                        if you were to draw the L on its side and define the verticies as 0-5 then 
                      n = side number
                      s = verticies needed to make the side.
                        n   s1  s2
                        1 | 0 | 5
                        2 | 1 | 0
                        3 | 2 | 1
                        4 | 3 | 2
                        5 | 4 | 3
                        6 | 5 | 4
                    */
                    $s1 = $n-1; $s2 = $n-2; if ($s2 < 0) { $s2 = count($points["x"])-1; }

                    $face["x"] = array($points["x"][$s1],$points["x"][$s1],$points["x"][$s1],$points["x"][$s1]);
                    $face["y"] = array($miny,$miny,$maxy,$maxy);
                    $face["z"] = array($points["z"][$s1],$points["z"][$s2],$points["z"][$s1],$points["z"][$s2]);
                    if (!isset($faces[0])) {
                        $faces[0] = $face;
                    } else {
                        array_push($faces, $face);
                    }
                    $face = null;
                }
                
                if($debug) { print_r($faces); }
                //isometric cube draw
                
                //generate faces
                foreach ($faces as $facenum => $face) {
                    $o = "   //{$label}:face-{$facenum}\n";
                    $o .= "   {\"label\": \"{$label}-face-{$facenum}\", \"color\": \"{$color}\", \"opacity\": 0.5, \"closed\": true, \"path\": [\n";
                    $o .= "     {\"x\": {$face["x"][0]}, \"y\": {$face["y"][0]}, \"z\": {$face["z"][0]}},\n";
                    $o .= "     {\"x\": {$face["x"][1]}, \"y\": {$face["y"][1]}, \"z\": {$face["z"][1]}},\n";
                    $o .= "     {\"x\": {$face["x"][2]}, \"y\": {$face["y"][2]}, \"z\": {$face["z"][2]}},\n";
                    $o .= "     {\"x\": {$face["x"][3]}, \"y\": {$face["y"][3]}, \"z\": {$face["z"][3]}}\n";
                    $o .= "   ]},\n";
                }
                    $output .= $o;
                
                
                $output = substr($output,0,-2);
                $output .= "\n";
            }
        }
    }	
   
    
    $output .= "]);";
    
    
    print $output;
 
?>