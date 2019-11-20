<?php

namespace ProVallo\Controllers\Backend;

use Favez\ORM\Entity\Entity;
use ProVallo\Plugins\Backend\Components\Controllers\API;
use ProVallo\Plugins\Backend\Models\User\User;

class UserController extends API
{
    
    public function configure ()
    {
        return [
            'model'          => User::class,
            'allowedActions' => [
                'status',
                'login'
            ]
        ];
    }
    
    public function statusAction ()
    {
        if (self::auth()->isLoggedIn())
        {
            /** @var \ProVallo\Plugins\Backend\Models\User\User $user */
            $user = self::auth()->getUser();
            
            return self::json()->success([
                'username' => $user->username
            ]);
        }
        
        return self::json()->failure();
    }
    
    public function loginAction ()
    {
        $username = self::request()->getParam('username');
        $password = self::request()->getParam('password');
        
        try
        {
            self::auth()->login($username, $password);
            
            return self::json()->success([
                'username' => $username
            ]);
        }
        catch (\Exception $ex)
        {
            return self::json()->failure([
                'message' => $ex->getMessage()
            ]);
        }
    }
    
    public function logoutAction ()
    {
        self::auth()->logout();
        
        return self::json()->success();
    }
    
    protected function getListQuery ()
    {
        $query = parent::getListQuery();
        
        $query->select(null)
            ->select('id, groupID, username, "" AS password, created, changed');
        
        return $query;
    }
    
    protected function map ($row)
    {
        $row['id']      = (int) $row['id'];
        $row['groupID'] = (int) $row['groupID'];
        
        return $row;
    }
    
    protected function setDefaultValues (Entity $entity)
    {
        $entity->created = date('Y-m-d H:i:s');
    }
    
    protected function setValues (Entity $entity, $input)
    {
        $entity->changed  = date('Y-m-d H:i:s');
        $entity->username = $input['username'];
        $entity->groupID  = $input['groupID'];
        
        if (!empty($input['password']))
        {
            $entity->password = password_hash($input['password'], PASSWORD_BCRYPT);
        }
    }
    
    protected function checkPermission (Entity $entity, $action)
    {
        switch ($action)
        {
            case 'remove':
                $userID = self::auth()->getUserID();
                
                if ($userID === (int) $entity->id) // Prevent suicide
                {
                    return false;
                }
            break;
        }
        
        return true;
    }
    
}