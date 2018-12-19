<?php

namespace ProVallo\Plugins\Backend\Migrations;

use ProVallo\Components\Database\Migration;

class Migration_2 extends Migration
{
    
    public function up ()
    {
        $password = password_hash('demo', PASSWORD_BCRYPT);
        
        $this->addSQL('INSERT INTO `user` (username, password) VALUES (?, ?)', [
            'demo',
            $password
        ]);
    }
    
    public function down ()
    {
        // TODO: Implement down() method.
    }
    
}