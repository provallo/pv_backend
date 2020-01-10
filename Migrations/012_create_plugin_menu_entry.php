<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;
use ProVallo\Core;

class Migration_12 extends Migration
{
    
    public function up ()
    {
        $id = Core::db()->from('menu')->where('label = ?', 'Config')->fetchColumn(0);
        
        $this->addSQL('
            INSERT INTO `menu` (parentID, label, route, position) VALUES
              (' . $id . ', "Plugins", "/config/plugins", 2)
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}