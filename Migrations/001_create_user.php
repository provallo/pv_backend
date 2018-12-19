<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;

class Migration_1 extends Migration
{
    
    public function up ()
    {
        $this->addSQL('
            CREATE TABLE `user` (
              `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `username` VARCHAR(255) NOT NULL,
              `password` VARCHAR(512) NOT NULL,
              `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `changed` DATETIME
            );
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}