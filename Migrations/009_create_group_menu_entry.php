<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;
use ProVallo\Core;

class Migration_9 extends Migration
{
    
    public function up ()
    {
        $id = Core::db()->from('menu')->where('label = ?', 'Users')->fetchColumn(0);
        
        $this->addSQL('
            INSERT INTO `menu` (parentID, label, route, position) VALUES
              (' . $id . ', "Groups", "/users/groups", 1)
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}