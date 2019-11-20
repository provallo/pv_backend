<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;

class Migration_7 extends Migration
{
    
    public function up ()
    {
        $this->addSQL('
            CREATE TABLE `user_group` (
              `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `name` VARCHAR(255) NOT NULL,
              `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `changed` DATETIME
            );
        ');
    
        $this->addSQL('
            ALTER TABLE `user` ADD COLUMN `groupID` INT(11) NOT NULL AFTER `id`
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}