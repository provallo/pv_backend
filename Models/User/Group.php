<?php

namespace ProVallo\Plugins\Backend\Models\User;

use Favez\Mvc\ORM\Entity;

class Group extends Entity
{
    
    const SOURCE = 'user_group';
    
    public $id;
    
    public $name;
    
    public $created;
    
    public $changed;
    
    public function initialize ()
    {
        $this->hasMany(User::class, 'groupID')->setName('users');
    }
    
}