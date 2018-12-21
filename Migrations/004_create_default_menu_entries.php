<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;

class Migration_4 extends Migration
{
    
    public function up ()
    {
        $this->addSQL('
            INSERT INTO `menu` (parentID, label, route, position) VALUES
              (-1, "Dashboard", "/", 0),
              (-1, "Users", "/users", 1);
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}