<?php
    $yml = @'<serverdir>\plugins\WorldGuard\worlds\<worldname>\regions.yml'; 
    $yml = @'regions.yml'; 
    $markersFile = @'../markers.js';
    $color_chestdeny = "#880000"; // what color should the region be when the flag chestaccess-deny is found?
    $color_normal = "#FFAA00"; // what color should normal regions be colored as?
    $backFaceCull = false; // render the wireframe faces of polygons that do not point towards the camera.
    $debug = true; // will break overviewer, but show the contents of the arrays... debuging only.
    
    require_once "spyc.php"; // YAML Library.
    require_once "classes.php"; // Classes Library
    
    $data = spyc_load_file($yml); 
    
    processMarkers($markersFile, $markers, $specialObjects);
    
    ///TODO - Process regions, check if region name exists in $markers, merge data and output accordingly
    
    /////////////////////////////////
    // FUNCTIONS
    /////////////////////////////////
    
    function processMarkers($path, &$markers, &$specialObjects)
    {
        $rawMarkers = file_get_contents($path);
        $rawMarkerItems = explode("},", $rawMarkers);
        
        //printf("rawr for gary %s\n", $rawMarkerItems[0]);
        /*
        rawr for gary overviewer.collections.markerDatas.push([
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
                        if ($so->GetType() == "DetailedRegion")
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
            if (isset($lines[0]) && $lines[0] != "")
            {
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