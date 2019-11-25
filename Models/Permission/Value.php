<?php

namespace ProVallo\Plugins\Backend\Models\Permission;

use Favez\Mvc\ORM\Entity;

class Value extends Entity
{
    
    const SHOULD_REMOVE_WITH_PARENT = true;
    
    const SOURCE = 'permission_value';
    
    public $id;
    
    public $permissionID;
    
    public $userID;
    
    public $groupID;
    
    public $value;
    
    public function initialize ()
    {
        $this->belongsTo(Permission::class, 'permissionID')->setName('permission');
    }
    
}