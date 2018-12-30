<?php

namespace ProVallo\Plugins\Backend\Models\Config;

use Favez\Mvc\ORM\Entity;

class Config extends Entity
{
    
    const SOURCE = 'config';
    
    public $id;
    
    public $name;
    
    public $label;
    
    public $data;
    
    public function initialize ()
    {
        $this->hasMany(Element::class, 'configID', 'id')->setName('elements');
    }
    
}