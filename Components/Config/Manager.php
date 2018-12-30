<?php

namespace ProVallo\Plugins\Backend\Components\Config;

use ProVallo\Components\Plugin\Bootstrap;
use ProVallo\Core;
use ProVallo\Plugins\Backend\Models\Config\Config;
use ProVallo\Plugins\Backend\Models\Config\Element;
use ProVallo\Plugins\Backend\Models\Config\Value;

class Manager
{
    
    /**
     * Creates a new configuration
     *
     * @param mixed $identifier     A unique identifier for the configuration
     * @param array $configuration  The configuration elements
     * @param array $additionalData Additional configuration
     */
    public function create ($identifier, $configuration, $additionalData = [])
    {
        $identifier = $this->convertIdentifier($identifier, $data);
        $configID   = $this->getIdByIdentifier($identifier);
        
        if ($configID > 0)
        {
            /** @var Config $config */
            $config   = Config::repository()->find($configID);
            $elements = $config->elements;
            $updated  = [];
            
            // Merge elements
            foreach ($configuration as $name => $data)
            {
                foreach ($elements as $element)
                {
                    if ($element->name === $name)
                    {
                        $element->data = json_encode($data);
                        $updated[$element->id] = true;
                        continue 2;
                    }
                }
                
                $element       = Element::create();
                $element->name = $name;
                
                $value = Value::create();
                
                if (isset($data['value']))
                {
                    $value->value = $data['value'];
                    unset ($data['value']);
                }
                
                $element->data  = json_encode($data);
                $element->value = $value;
                
                $elements[] = $element;
            }
            
            foreach ($elements as $i => $element)
            {
                if ((int) $element->id > 0 && !isset($updated[$element->id]))
                {
                    Element::repository()->remove($element);
                    unset ($elements[$i]);
                }
            }
        }
        else
        {
            /** @var Config $config */
            $config       = Config::create();
            $config->name = $identifier;
            
            $elements = [];
            
            foreach ($configuration as $name => $data)
            {
                $element       = Element::create();
                $element->name = $name;
                
                $value = Value::create();
                
                if (isset($data['value']))
                {
                    $value->value = $data['value'];
                    unset ($data['value']);
                }
                
                $element->data  = json_encode($data);
                $element->value = $value;
                
                $elements[] = $element;
            }
        }
        
        $config->elements = $elements;
        $this->applyData($config, $additionalData);
        $config->save();
    }
    
    /**
     * Get configuration
     *
     * @param mixed $identifier
     *
     * @return mixed
     * @throws \Exception
     */
    public function get ($identifier)
    {
        $identifier = $this->convertIdentifier($identifier);
        $config     = Config::repository()->findOneBy(['name' => $identifier]);
        
        if ($config instanceof Config)
        {
            $result = [];
            
            foreach ($config->elements as $element)
            {
                $value = $element->value->value;
                $data  = json_decode($element->data, true);
                
                switch ($data['type'])
                {
                    case 'number':
                    case 'select':
                        $value = (int) $value;
                    break;
                    case 'checkbox';
                        $value = (int) $value === 1;
                    break;
                }
                
                $result[$element->name] = $value;
            }
            
            return $result;
        }
        
        return null;
    }
    
    protected function applyData (Config $config, $data)
    {
        $config->label = $data['label'] ?? $config->name;
        
        if (isset($data['label']))
        {
            unset ($data['label']);
        }
        
        $config->data = json_encode($data);
    }
    
    /**
     * @param string $identifier
     *
     * @return integer
     */
    protected function getIdByIdentifier ($identifier)
    {
        return (int) Core::db()->from('config')->where('name = ?', $identifier)->fetchColumn();
    }
    
    /**
     * Converts given identifier to unique string.
     *
     * The identifier is usually a string containing the config name but can
     * also be an instanceof \ProVallo\Components\Plugin\Bootstrap
     *
     * @param mixed $identifier
     * @param mixed $data
     *
     * @return string
     */
    protected function convertIdentifier ($identifier, &$data = [])
    {
        if ($identifier instanceof Bootstrap)
        {
            $data['label'] = 'Plugin: ' . $identifier->getInfo()->getLabel();
            
            return 'plugin_' . $identifier->getInstance()->getModel()->id;
        }
        
        return (string) $identifier;
    }
    
}