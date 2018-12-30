<?php

namespace ProVallo\Controllers\Backend;

use Favez\ORM\Entity\Entity;
use ProVallo\Plugins\Backend\Components\Controllers\API;
use ProVallo\Plugins\Backend\Models\Config\Config;

class ConfigController extends API
{
    
    public function configure ()
    {
        return [
            'model'  => Config::class,
            'detail' => [
                'recursive' => true
            ]
        ];
    }
    
    protected function setValues (Entity $entity, $input)
    {
        foreach ($input['elements'] as $element)
        {
            foreach ($entity->elements as $el)
            {
                if ((int) $el->id === (int) $element['id'])
                {
                    $el->value->value = $element['value']['value'];
                }
            }
        }
    }
    
}