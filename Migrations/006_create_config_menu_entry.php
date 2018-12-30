<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;

class Migration_6 extends Migration
{
    
    public function up ()
    {
        $this->addSQL('
            INSERT INTO `menu` (parentID, label, route, position) VALUES
              (-1, "Config", "/config", 9999)
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}