<?php

namespace ProVallo\Plugins\Backend\Models\Menu;

use Favez\Mvc\ORM\Entity;

class Menu extends Entity
{
    
    const SOURCE = 'menu';
    
    public $id;
    
    public $parentID;
    
    public $label;
    
    public $route;
    
    public $position;
    
}