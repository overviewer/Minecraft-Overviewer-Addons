<?php
class Point
{
    public $X = 0;
    public $Y = 0;
    public $Z = 0;
    
    function __construct($x, $y, $z)
    {
        $this->X = $x;
        $this->Y = $y;
        $this->Z = $z;
    }
}

class Face
{
    public $Points;
    
    function __construct($points)
    {
        $this->Points = $points;
    }
}

interface IOutput
{
    function Output();
}

class SpecialObject implements IOutput // short bus!
{
    protected $__name;
    //protected $__varType;
    
    /*
    public function __construct()
    {
        $this->__varType = "SpecialObject";
    }
    */
    
    public function Name($name=null) 
    {
        if ($name != null)
            $this->__name = $name;
        return $this->__name;
    }
    
    /*
    public function GetType()
    {
        return $this->__varType;
    }
    */
    
    public function Output()
    {
        return "";
    }
}

class Region extends SpecialObject
{
    protected $__type;
    protected $__flags;
    protected $__owners;
    protected $__members;
    protected $__priority;
    protected $__points;
    protected $__regionColor;
    
    function __construct() 
    {
        $this->__type = null;
        $this->__flags = null;
        $this->__owners = null;
        $this->__members = null;
        $this->__priority = null;
        $this->__name = null;
        $this->__faces = null;
        $this->__regionColor = null;
        $this->__varType = "Region";
    }
    
    public function Type($type=null) 
    {
        if ($type != null)
            $this->__type = $type;
        return $this->__type;
    }
    public function Faces($faces=null) 
    {
        if ($faces != null)
            $this->__faces = $faces;
        return $this->__faces;
    }
    
    public function RegionColor($regionColor=null) 
    {
        if ($regionColor != null)
            $this->__regionColor = $regionColor;
        return $this->__regionColor;
    }
    
    ///TODO get/sets for the rest of the vars.
    
    public function Output()
    {
        //$output = "<< Temp: Output for Region >>";
        $output = "";

        /*
        $output .= "   //{$label}:top layer\n";
        $output .= "   {\"label\": \"{$label}-top\", \"color\": \"{$color}\", \"opacity\": 0.5, \"closed\": true, \"path\": [\n";
        $output .= "     {\"x\": {$minx}, \"y\": {$maxy}, \"z\": {$minz}},\n";
        $output .= "     {\"x\": {$minx}, \"y\": {$maxy}, \"z\": {$maxz}},\n";
        $output .= "     {\"x\": {$maxx}, \"y\": {$maxy}, \"z\": {$maxz}},\n";
        $output .= "     {\"x\": {$maxx}, \"y\": {$maxy}, \"z\": {$minz}}\n";
        $output .= "   ]},\n";
        */
        
        if ($this->__faces != null)
        {
            foreach ($this->__faces as $face)
            {
                $output .= "   {\"label\": \"{$this->__name}\", \"color\": \"{$this->__regionColor}\", \"opacity\": 0.5, \"closed\": true, \"path\":\n   [\n";
                $pointOutput = "";
                foreach ($face->Points as $point)
                {
                    if ($pointOutput != "")
                        $pointOutput .= ",\n";
                        
                    $pointOutput .= "      {\"x\": {$point->X}, \"y\": {$point->Y}, \"z\": {$point->Z}}";  
                }
                $output .= $pointOutput . "\n   ]},\n";
            }
        }
        else
        {
            $output .= "//Error -- No faces on object, " . $this->Name();
        }
        
        return $output;
    }
}

class DetailedRegion extends Region
{
    private $__label;
    private $__labelColor;
    private $__lineOpacity;
    private $__fillOpactiy;
    private $__closed;
    
    function __construct()
    {
        parent::__construct();
        $this->__label = null;
        $this->__labelColor = null;
        $this->__lineOpacity = null;
        $this->__fillOpacity = null;
        $this->__closed = null;
        $this->__varType = "DetailedRegion";
    }
    
    public function Label($label=null) 
    {
        if ($label != null)
            $this->__label = $label;
        return $this->__label;
    }
    public function LabelColor($labelColor=null) 
    {
        if ($labelColor != null)
            $this->__labelColor = $labelColor;
        return $this->__labelColor;
    }
    public function LineOpacity($lineOpacity=null) 
    {
        if ($lineOpacity != null)
            $this->__lineOpacity = $lineOpacity;
        return $this->__lineOpacity;
    }
    public function FillOpacity($fillOpacity=null) 
    {
        if ($fillOpacity != null)
            $this->__fillOpacity = $fillOpacity;
        return $this->__fillOpacity;
    }
    public function Closed($closed=null) 
    {
        if ($closed != null)
            $this->__closed = $closed;
        return $this->__closed;
    }

    public function Merge(DetailedRegion $other)
    {
        $dr = new DetailedRegion();
        
        $dr->Name(($other->Name() != null) ? $other->Name() : $this->Name());
        $dr->Label(($other->Label() != null) ? $other->Label() : $this->Label());
        $dr->LabelColor(($other->LabelColor() != null) ? $other->LabelColor() : $this->LabelColor());
        $dr->RegionColor(($other->RegionColor() != null) ? $other->RegionColor() : $this->RegionColor());
        $dr->LineOpacity(($other->LineOpacity() != null) ? $other->LineOpacity() : $this->LineOpacity());
        $dr->FillOpacity(($other->FillOpacity() != null) ? $other->FillOpacity() : $this->FillOpacity());
        $dr->Closed(($other->Closed() != null) ? $other->Closed() : $this->Closed());
        
        return $dr;
    }
    
    public function Output()
    {
        $output = "//<< Temp: Output for DetailedRegion >>";
        // foreach through faces, if !closed, break (only output top face, don't use closed in google API)
        
        return $output;
    }
    
}

class Marker
{
    private $__chunk;
    private $__msg;
    private $__type;
    private $__location;
    
    ///TODO chunk
    public function Message($msg=null) 
    {
        if ($msg != null)
            $this->__msg = $msg;
        return $this->__msg;
    }
    public function Type($type=null) 
    {
        if ($type != null)
            $this->__type = $type;
        return $this->__type;
    }
    ///TODO location
}

?>