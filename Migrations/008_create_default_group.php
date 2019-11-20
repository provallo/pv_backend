<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;

class Migration_8 extends Migration
{
    
    public function up ()
    {
        $this->addSQL('
            INSERT INTO `user_group` (`name`) VALUES ("Default")
        ');
    
        $this->addSQL('
            UPDATE `user` SET `groupID` = 1
        ');
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}