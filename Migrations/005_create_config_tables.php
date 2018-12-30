<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;

class Migration_5 extends Migration
{
    
    public function up ()
    {
        $this->addSQL('
            CREATE TABLE `config` (
              `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `name` VARCHAR(255) NOT NULL UNIQUE,
              `label` VARCHAR(255) NOT NULL,
              `data` BLOB
            );

            CREATE TABLE `config_element` (
              `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `configID` INT(11) NOT NULL,
              `name` VARCHAR(255) NOT NULL,
              `data` BLOB NOT NULL
            );

            CREATE TABLE `config_value` (
              `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `elementID` INT(11) NOT NULL,
              `value` BLOB
            );
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}