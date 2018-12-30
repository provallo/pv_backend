<?php

namespace ProVallo\Plugins\Backend\Models\Config;

use Favez\Mvc\ORM\Entity;

class Element extends Entity
{
    
    const SOURCE = 'config_element';
    
    const SHOULD_UPDATE_WITH_PARENT = true;
    
    const SHOULD_REMOVE_WITH_PARENT = true;
    
    public $id;
    
    public $configID;
    
    public $name;
    
    public $data;
    
    public function initialize ()
    {
        $this->hasOne(Value::class, 'elementID', 'id')->setName('value');
        $this->belongsTo(Config::class, 'configID', 'id')->setName('config');
    }
    
}