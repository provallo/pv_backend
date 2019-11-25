<?php

namespace ProVallo\Controllers\Backend;

use Favez\ORM\Entity\Entity;
use ProVallo\Core;
use ProVallo\Plugins\Backend\Components\Controllers\API;
use ProVallo\Plugins\Backend\Models\Permission\Value;
use ProVallo\Plugins\Backend\Models\User\Group;
use ProVallo\Plugins\Backend\Models\User\User;

class GroupController extends API
{
    
    public function configure ()
    {
        return [
            'model' => Group::class
        ];
    }
    
    protected function getListQuery ()
    {
        $query = parent::getListQuery();
        
        $query->select(null)
            ->select('id, name');
        
        return $query;
    }
    
    protected function map ($row)
    {
        $row['id'] = (int) $row['id'];
        
        return $row;
    }
    
    protected function setDefaultValues (Entity $entity)
    {
        $entity->created = date('Y-m-d H:i:s');
    }
    
    protected function setValues (Entity $entity, $input)
    {
        $entity->changed = date('Y-m-d H:i:s');
        $entity->name    = $input['name'];
    }
    
    protected function checkPermission (Entity $entity, $action)
    {
        if ($action === 'remove')
        {
            $count = Core::db()->from('user')->where('groupID = ?', $entity->id)->count();
            
            return $count === 0;
        }
        
        return true;
    }
    
    protected function afterSave (Entity $entity, $isNew)
    {
        if ($permissions = self::request()->getParam('permissions'))
        {
            foreach ($permissions as $permission)
            {
                $value = Value::repository()->findOneBy([
                    'id' => $permission['value']['id'],
                ]);
                if (!isset($value))
                {
                    $value               = Value::create();
                    $value->permissionID = $permission['id'];
                    $value->groupID      = $entity->id;
                }
                
                $value->value = (int) $permission['value']['value'];
                $value->save();
            }
        }
    }
    
}