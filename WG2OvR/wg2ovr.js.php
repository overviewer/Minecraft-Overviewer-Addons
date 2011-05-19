<?php
    require_once "spyc.php"; // YAML Library.
    require_once "classes.php"; // Classes Library

    $yml = @'<serverdir>\plugins\WorldGuard\worlds\<worldname>\regions.yml'; 
    $yml = @'../regions.yml'; 
    $markersFile = @'../markers.js';
    $color_chestdeny = "#880000"; // what color should the region be when the flag chestaccess-deny is found?
    $color_normal = "#FFAA00"; // what color should normal regions be colored as?
    $backFaceCull = false; // render the wireframe faces of polygons that do not point towards the camera.
    $debug = true; // will break overviewer, but show the contents of the arrays... debuging only.
    
    $finalOutput = "// Exported from WorldGuard v5x Regions file\n";
    $finalOutput .= "// Coded by Michael Writhe - michael[at]writhem[dot]com\n";
    $finalOutput .= "// http://goo.gl/dc0tV\n";
    $finalOutput .= "overviewer.collections.regionDatas.push([\n";
    $output = "";
    
    $data = spyc_load_file($yml); 
        
    processMarkers($markersFile, $markers, $specialObjects);
    
    $lines = file($yml);
    foreach ($data["regions"] as $name => $regionRaw)
    {
        // Build general region information
        $region = new Region();
        $region->Name($name);
        $region->Type($regionRaw["type"]);
        
        // Build point information
        //if ($region->Type() == "cuboid")
        switch ($region->Type())
        {
            case "cuboid":
            {
                $color = $color_normal;
                if(isset($regionRaw["flags"]["chest-access"]))
                {
                    if ($regionRaw["flags"]["chest-access"] == "deny")
                    {
                        $color = $color_chestdeny; // no chest access = red
                    }
                }
                $region->RegionColor($color);
            
                $minx = $regionRaw["min"]["x"];
                $miny = $regionRaw["min"]["y"];
                $minz = $regionRaw["min"]["z"];
                $maxx = $regionRaw["max"]["x"];
                $maxy = $regionRaw["max"]["y"];
                $maxz = $regionRaw["max"]["z"];
                
                //center
                $regionFaces = $region->Faces();
                
                // Create the top face
                $region->Faces(
                    array(
                        new Face(
                            array
                            (
                                new Point($minx, $maxy, $minz),
                                new Point($minx, $maxy, $maxz),
                                new Point($maxx, $maxy, $maxz),
                                new Point($maxx, $maxy, $minz)
                            )
                        )
                    )
                );
                
                //determine if drawing a cube is worth our time, 2 extra polygons for cube rendering.
                // if the bottom is more than 3m lower than the top, cube is worth it.
                if(abs($miny-$maxy)>3)
                {
                    $faceArray = $region->Faces();
                    array_push($faceArray,
                        new Face(
                            array
                            (
                                new Point($minx, $miny, $minz),
                                new Point($minx, $miny, $maxz),
                                new Point($minx, $maxy, $maxz),
                                new Point($minx, $maxy, $minz)
                            )                        
                        )
                    );
                    
                    array_push($faceArray,
                        new Face(
                            array
                            (
                                new Point($maxx, $maxy, $maxz),
                                new Point($minx, $maxy, $maxz),
                                new Point($minx, $miny, $maxz),
                                new Point($maxx, $miny, $maxz)
                            )                        
                        )
                    );
                    
                    $region->Faces($faceArray);
                }
                break;
            }
            case "poly2d":
            {
                break;
            }
            default:
            {
                printf("//Unrecognized region type: %s\n", $region->Type());
                break;
            }
        }
               
        $currentOutput = $region->Output();
        
        if (isset($specialObjects[$region->Name()]))
        {
            $so = $specialObjects[$region->Name()];
            if ($so instanceof DetailedRegion)
            {
                $so->Faces($region->Faces());
                $currentOutput = $so->Output();
            }
        }
        
        if ($output != "")
            $output .= ",\n";
            
        $output .= $currentOutput;
    }
    
    $finalOutput .= $output;
    $finalOutput .= "\n]);";
    
    print $finalOutput;
    
    if (0)
    {
    foreach ($data["regions"] as $label => $region) {
        //print_r($data);
        

        
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
                for ($n = 0; $n <= (count($points["x"])-1);$n++) {
                    /*
                        if you were to draw the L on its side and define the verticies as 0-5 then 
                      n = side number
                      s = verticies needed to make the side.
                        n   s1  s2
                        0 | 0 | 5
                        1 | 1 | 0
                        2 | 2 | 1
                        3 | 3 | 2
                        4 | 4 | 3
                        5 | 5 | 4
                    */
                    $s1 = $n; $s2 = $n-1; if ($s2 < 0) { $s2 = count($points["x"])-1; }

                    $face["x"] = array($points["x"][$s1],$points["x"][$s1],$points["x"][$s2],$points["x"][$s2]);
                    $face["y"] = array($maxy,$miny,$miny,$maxy);
                    $face["z"] = array($points["z"][$s1],$points["z"][$s1],$points["z"][$s2],$points["z"][$s2]);
                    
                    // here comes the shit ton of crazy math.
                    // to calculate the vector between two points A,B its:
                    // BA = B - A = (Bx - Ax, By - Ay, Bz - Az)
                    $a["x"] = $face["x"][0];
                    $a["y"] = $face["y"][0];
                    $a["z"] = $face["z"][0];
                    
                    $b["x"] = $face["x"][1];
                    $b["y"] = $face["y"][1];
                    $b["z"] = $face["z"][1];
                    
                    $c["x"] = $face["x"][3]; 
                    $c["y"] = $face["y"][3];
                    $c["z"] = $face["z"][3];
                    
                    $ba["x"] = $b["x"] - $a["x"];
                    $ba["y"] = $b["y"] - $a["y"];
                    $ba["z"] = $b["z"] - $a["z"];
                    
                    $ca["x"] = $c["x"] - $a["x"];
                    $ca["y"] = $c["y"] - $a["y"];
                    $ca["z"] = $c["z"] - $a["z"];
                    
                    $norm["x"] = round($ca["y"] * $ba["z"] - $ba["y"] * $ca["z"],0);
                    $norm["y"] = round($ca["z"] * $ba["x"] - $ba["z"] * $ca["x"],0);
                    $norm["z"] = round($ca["x"] * $ba["y"] - $ba["x"] * $ca["y"],0);
                    
                    if ($norm["x"] > 0 || $norm["z"] < 0) {
                        // do nothing
                    } else {
                        if (!isset($faces[0])) {
                            $faces[0] = $face;
                        } else {
                            array_push($faces, $face);
                        }
                    }
                    if ($debug) { 
                        print ("//debug: normal for point {$n} = ".$norm["x"] .",". $norm["y"].",".$norm["z"]."\n"); 
                        $o = "   //rawr test\n";
                        $o .= "   {\"label\": \"test-vector\", \"color\": \"#FF0000\", \"opacity\": 0.5, \"closed\": true, \"path\": [\n";
                        $o .= "     {\"x\": {$points["x"][$s1]}, \"y\": {$maxy}, \"z\": {$points["z"][$s1]}},\n";
                        $o .= "     {\"x\": ".($points["x"][$s1]+$norm["x"]).", \"y\": ".($maxy+$norm["y"]).", \"z\": ".($points["z"][$s1]+$norm["z"])."},\n";
                        $o .= "   ]},\n";
                        $output .= $o;
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
                    $output .= $o;
                }
            }
        }
    }	
   
    
    $output .= "]);";
    
    
    print $output;
    }
    
    /////////////////////////////////
    // FUNCTIONS
    /////////////////////////////////
    
    function processMarkers($path, &$markers, &$specialObjects)
    {
        $rawMarkers = file_get_contents($path);
        $rawMarkerItems = explode("},", $rawMarkers);
        
        //printf("rawr %s\n", $rawMarkerItems[0]);
        /*
        rawr overviewer.collections.markerDatas.push([
        {"msg": "Spawn", "y": 65, "x": -296, "chunk": [8, 7], "z": 103, "type": "spawn"
        */
        
        for($n = 0;$n < count($rawMarkerItems);$n++)
        {
            $decodeString = "";
            if ($n == count($rawMarkerItems) - 1)
            {
                $item = explode("]);",$rawMarkerItems[$n]);
                $decodeString = $item[0];
            }
            else if ($n == 0)
            {
                $item = explode("([", $rawMarkerItems[$n]);
                $decodeString = $item[1] . "}";
            }
            else
            {
                $decodeString = $rawMarkerItems[$n] . "}";
            }
            $json = json_decode($decodeString);
            $marker = buildMarker($json);
            
            if ($marker == null)
            {
                // OUTPUT ERROR OF SOME KIND
                die("////Invalid JSON format for the following: \n////{$decodeString}");
            }
            else // Marker is good
            {
                if (!isset($markers))
                {
                    $markers[0] = $marker;
                }
                else
                {
                    array_push($markers, $marker);
                }
                //printf("\\n : %d",$n);
                //print_r($json);
                
                $so = getSpecialObjectFromMarker($marker);
                if ($so != null)
                {
                    // This marker is a detailed region... carry on
                    
                    if (isset($specialObjects[$so->Name()]))
                    {
                        // merge data
                        //if ($so->GetType() == "DetailedRegion")
                        if ($so instanceof DetailedRegion)
                        {
                            $mergedDR = $so->Merge($specialObjects[$so->Name()]);
                            $specialObjects[$so->Name()] = $mergedDR;
                        }
                    }
                    else
                    {
                        $specialObjects[$so->Name()] = $so;
                    }
                }
            }
        }
        //$lastItem = 
        //$json = json_decode($lastItem[0]);
        //print_r($json);    
    }

    function buildMarker($rawMarker)
    {
        $marker = null;
        
        if ($rawMarker != null)
        {
            $marker = new Marker();
            $marker->Message($rawMarker->msg);
            $marker->Type($rawMarker->type);
        }
        
        return $marker;
    }
    
    function getSpecialObjectFromMarker(Marker $marker)
    {
        $so = null;
        
        if ($marker != null && $marker->Message() != null && $marker->Message() != "")
        {
            $lines = explode("\n", $marker->Message());
            //foreach ($lines as $linenum => $line) {
                //printf("parsing lines currently on line %s \n",$line);
            //}
            if (isset($lines[0]) && $lines[0] != "")
            {
                //print_r($lines);
                ///TODO: if first line is 'RI:' then the rest need to be searched for tokens too...
                $tokens = explode(":", $lines[0]);
                if (isset($tokens[0]) && isset($tokens[1]) && $tokens[0] != "")
                {
                    switch($tokens[0])
                    {
                        case "RI":
                            $so = new DetailedRegion();
                            ///TODO - Concat the remaining tokens... right now assuming only one colon in msg string
                            $so->Name($tokens[1]);
                            populateDetailedRegionFromLines($so, $lines);
                            break;
                        // MORE CASES HERE
                    }
                }
            }
        }
        
        return $so;
    }
    
    function populateDetailedRegionFromLines(DetailedRegion &$dr, $lines)
    {
        if ($lines != null)
        {
            for ($n = 0; $n < count($lines); $n++)
            {
                $tokens = explode(":", $lines[$n]);
                if (isset($tokens[0]) && isset($tokens[1]) && $tokens[0] != "")
                {
                    $code = $tokens[0];
                    $value = $tokens[1];
                    
                    switch ($code)
                    {
                        case "RC": // Region Colour
                            $dr->RegionColor($value);
                            break;
                        case "LO": // Line Opacity
                            $dr->LineOpacity($value);
                            break;
                        case "FO": // Fill Opacity
                            $dr->FillOpacity($value);
                            break;
                        case "CL": // Closed
                            $dr->Closed($value);
                            break;
                        case "L":  // Label
                            if ($dr->Label() == null) $dr->Label("");
                            $dr->Label($dr->Label() . $value);
                            break;
                        case "LC": // Label Colour
                            $dr->LabelColor($value);
                            break;
                    }
                }
            }
        }
    }
?>