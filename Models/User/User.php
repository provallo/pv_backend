<?php

namespace ProVallo\Plugins\Backend\Models\User;

use Favez\Mvc\ORM\Entity;
use ProVallo\Core;
use Validator\Validator;

class User extends Entity
{
    
    const SOURCE = 'user';
    
    public $id;
    
    public $groupID;
    
    public $username;
    
    public $password;
    
    public $created;
    
    public $changed;
    
    public function initialize ()
    {
        $this->belongsTo(Group::class, 'userID')->setName('group');
    }
    
    public function validate ()
    {
        Validator::addGlobalRule('user.unique_username', function ($fields, $value, $params) {
            $appID = Core::request()->getParam('id');
    
            return self::isUniqueusername($appID, $value);
        });
        
        return [
            'username' => [
                'required'             => 'Please enter a username',
                'user.unique_username' => 'The entered username is already in use'
            ],
            'password' => [
                'required' => 'Please enter a password'
            ]
        ];
    }
    
    public static function isUniqueUsername ($userID, $username)
    {
        $query = Core::db()->from(self::SOURCE)
            ->where('username = ?', $username);
        
        if ($userID > 0)
        {
            $query->where('id != ?', $userID);
        }
        
        return $query->count() === 0;
    }
    
}