<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;
use ProVallo\Core;

class Migration_11 extends Migration
{
    
    public function up ()
    {
        $id = Core::db()->from('menu')->where('label = ?', 'Users')->fetchColumn(0);
        
        $this->addSQL('
            INSERT INTO `menu` (parentID, label, route, position) VALUES
              (' . $id . ', "Permissions", "/users/permissions", 2)
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}