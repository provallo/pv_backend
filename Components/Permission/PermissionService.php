<?php

namespace ProVallo\Plugins\Backend\Components\Permission;

use Favez\Mvc\DI\Injectable;
use ProVallo\Core;
use ProVallo\Plugins\Backend\Models\Permission\Permission;
use ProVallo\Plugins\Backend\Models\User\User;

class PermissionService
{
    use Injectable;
    
    public function add ($name, $defaultValue = 0, $label = null, $pluginID = null)
    {
        /** @var Permission $permission */
        $permission = Permission::repository()->findOneBy(['name' => $name]);
        
        if (!$permission)
        {
            $permission = Permission::create();
        }
        
        $permission->name         = $name;
        $permission->defaultValue = $defaultValue;
        $permission->label        = $label;
        $permission->pluginID     = $pluginID;
        
        Permission::repository()->save($permission);
        
        return $permission->id;
    }
    
    public function validateUserID ($name, $userID)
    {
        $sql = '
            SELECT IF(pv.id, pv.value, p.defaultValue)
            FROM permission p
            LEFT JOIN user u ON u.id = ?
            LEFT JOIN permission_value pv ON pv.permissionID = p.id
            WHERE p.name = ?
              AND pv.groupID = u.groupID
        ';
        
        $stmt = Core::db()->query($sql);
        $stmt->bindValue(1, $userID);
        $stmt->bindValue(2, $name);
        
        if ($stmt->execute())
        {
            $value = (int) $stmt->fetchColumn();
            
            return $value === 1;
        }
        
        throw new \Exception('Unable to check permissions.');
    }
    
    public function validate ($name, User $user)
    {
        $sql = '
            SELECT IF(pv.id, pv.value, IF(pv2.id, pv2.value, p.defaultValue))
            FROM permission p
            LEFT JOIN permission_value pv ON pv.userID = ?
            LEFT JOIN permission_value pv2 ON pv2.groupID = ?
            WHERE p.name = ?
        ';
        
        $stmt = Core::db()->query($sql);
        $stmt->bindValue(1, $user->id);
        $stmt->bindValue(2, $user->groupID);
        $stmt->bindValue(3, $name);
        
        if ($stmt->execute())
        {
            $value = (int) $stmt->fetchColumn();
            
            return $value === 1;
        }
        
        throw new \Exception('Unable to check permissions.');
    }
    
}