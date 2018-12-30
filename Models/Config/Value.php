<?php

namespace ProVallo\Plugins\Backend\Models\Config;

use Favez\Mvc\ORM\Entity;

class Value extends Entity
{
    
    const SOURCE = 'config_value';
    
    const SHOULD_UPDATE_WITH_PARENT = true;
    
    const SHOULD_REMOVE_WITH_PARENT = true;
    
    public $id;
    
    public $elementID;
    
    public $value;
    
    public function initialize ()
    {
        $this->belongsTo(Element::class, 'elementID', 'id')->setName('element');
    }
    
}