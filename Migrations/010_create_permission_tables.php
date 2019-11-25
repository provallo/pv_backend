<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;

class Migration_10 extends Migration
{
    
    public function up ()
    {
        $this->addSQL('
            CREATE TABLE `permission` (
                `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `defaultValue` TINYINT(1) NOT NULL DEFAULT \'0\',
                `label` VARCHAR(255),
                `pluginID` INT(11)
            )
        ');
    
        $this->addSQL('
            CREATE TABLE `permission_value` (
                `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `permissionID` INT(11) NOT NULL,
                `userID` INT(11),
                `groupID` INT(11),
                `value` TINYINT(1) NOT NULL
            )
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}