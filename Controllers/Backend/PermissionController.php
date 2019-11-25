<?php

namespace ProVallo\Controllers\Backend;

use Favez\ORM\Entity\Entity;
use ProVallo\Plugins\Backend\Components\Controllers\API;
use ProVallo\Plugins\Backend\Models\Permission\Permission;
use ProVallo\Plugins\Backend\Models\Permission\Value;

class PermissionController extends API
{
    
    public function configure ()
    {
        return [
            'model' => Permission::class
        ];
    }
    
    protected function getListQuery ()
    {
        $query = parent::getListQuery();
        
        $query->select(null)
            ->select('id, name, defaultValue, label');
        
        return $query;
    }
    
    protected function map ($row)
    {
        $row['id'] = (int) $row['id'];
        
        if (($groupID = (int) self::request()->getParam('groupID')) > 0)
        {
            $value = Value::repository()->findOneBy([
                'permissionID' => $row['id'],
                'groupID'      => $groupID
            ]);
            
            if (!isset($value))
            {
                $value = [
                    'id'    => -1,
                    'value' => $row['defaultValue']
                ];
            }
            
            $row['value'] = [
                'id'    => (int) $value['id'],
                'value' => (bool) $value['value']
            ];
        }
        
        return $row;
    }
    
    protected function setDefaultValues (Entity $entity)
    {
    }
    
    protected function setValues (Entity $entity, $input)
    {
        $entity->name         = $input['name'];
        $entity->defaultValue = (int) $input['defaultValue'];
        $entity->label        = $input['label'];
    }
    
    protected function checkPermission (Entity $entity, $action)
    {
        return true;
    }
    
}