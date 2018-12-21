<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;

class Migration_3 extends Migration
{
    
    public function up ()
    {
        $this->addSQL('
            CREATE TABLE `menu` (
              `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `parentID` INT(11) NOT NULL,
              `label` VARCHAR(255) NOT NULL,
              `route` VARCHAR(255) NOT NULL,
              `position` INT(11)
            );
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}