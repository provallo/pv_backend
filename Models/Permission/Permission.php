<?php

namespace ProVallo\Plugins\Backend\Models\Permission;

use Favez\Mvc\ORM\Entity;

class Permission extends Entity
{
    
    const SOURCE = 'permission';
    
    public $id;
    
    public $name;
    
    public $defaultValue;
    
    public $label;
    
    public $pluginID;
    
    public function initialize ()
    {
        $this->hasMany(Value::class, 'permissionID')->setName('values');
    }
    
}