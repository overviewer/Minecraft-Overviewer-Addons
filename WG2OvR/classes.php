<?php
class Point
{
    public $x = 0;
    public $y = 0;
    public $z = 0;
}

class SpecialObject // short bus!
{
    protected $__name;
    protected $__varType;
    
    public function __construct()
    {
        $this->__varType = "SpecialObject";
    }
    
    public function Name($name=null) 
    {
        if ($name != null)
            $this->__name = $name;
        return $this->__name;
    }
    
    public function GetType()
    {
        return $this->__varType;
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
    
    function __construct() 
    {
        $this->__type = null;
        $this->__flags = null;
        $this->__owners = null;
        $this->__members = null;
        $this->__priority = null;
        $this->__name = null;
        $this->__points = null;
        $this->__varType = "Region";
    }
    
    public function Type($type=null) 
    {
        if ($type != null)
            $this->__type = $type;
        return $this->__type;
    }
    public function Points($points=null) 
    {
        if ($points != null)
            $this->__points = points;
        return $this->__points;
    }
    
    ///TODO get/sets for the rest of the vars.
    
}

class DetailedRegion extends Region
{
    private $__label;
    private $__labelColor;
    private $__regionColor;
    private $__lineOpacity;
    private $__fillOpactiy;
    private $__closed;
    
    function __construct()
    {
        parent::__construct();
        $this->__label = null;
        $this->__labelColor = null;
        $this->__regionColor = null;
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
    public function RegionColor($regionColor=null) 
    {
        if ($regionColor != null)
            $this->__regionColor = $regionColor;
        return $this->__regionColor;
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
        $dr = new DetailRegion();
        
        $dr->Label(($other->Label() != null) ? $other->Label() : $this->Label());
        $dr->LabelColor(($other->LabelColor() != null) ? $other->LabelColor() : $this->LabelColor());
        $dr->RegionColor(($other->RegionColor() != null) ? $other->RegionColor() : $this->RegionColor());
        $dr->LineOpacity(($other->LineOpacity() != null) ? $other->LineOpacity() : $this->LineOpacity());
        $dr->FillOpacity(($other->FillOpacity() != null) ? $other->FillOpacity() : $this->FillOpacity());
        $dr->Closed(($other->Closed() != null) ? $other->Closed() : $this->Closed());
        
        return $dr;
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