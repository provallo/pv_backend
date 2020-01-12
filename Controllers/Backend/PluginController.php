<?php

namespace ProVallo\Controllers\Backend;

use Favez\ORM\Entity\Entity;
use ProVallo\Components\Job\JobRunner;
use ProVallo\Components\Plugin\Updater\Update;
use ProVallo\Models\Plugin\Plugin;
use ProVallo\Plugins\Backend\Components\Controllers\API;
use Symfony\Component\Console\Output\BufferedOutput;

class PluginController extends API
{
    
    public function configure ()
    {
        return [
            'model' => Plugin::class
        ];
    }
    
    protected function getListQuery ()
    {
        $query = parent::getListQuery();
        
        $query->select(null)
            ->select('id, active, name, label, description, version, author, email, website, created, changed');
        
        return $query;
    }
    
    protected function map ($row)
    {
        $row['id'] = (int) $row['id'];
        
        $plugin = self::plugins()->get($row['name']);
        $update = self::plugins()->getUpdater()->checkForUpdate($plugin->getInstance());
        
        if ($update instanceof Update)
        {
            $row['remoteUpdate'] = $update->getVersion();
        }
        
        if ($plugin->getInfo()->getVersion() !== $row['version'])
        {
            $row['localUpdate'] = $plugin->getInfo()->getVersion();
        }
        
        return $row;
    }
    
    protected function checkPermission (Entity $entity, $action)
    {
        return $action === 'detail';
    }
    
    public function installAction ()
    {
        $name = self::request()->getParam('name');
        
        try
        {
            $result = self::plugins()->install($name);
            
            if ($result->isSuccess()) {
                if ($result->hasJobs()) {
                    $output = new BufferedOutput();
                    $runner = new JobRunner($output);
                    $runner->run($result->getJobs());
                    
                    self::json()->assign('jobOutput', $output->fetch());
                }
                
                return self::json()->success();
            }
            
            return self::json()->failure([
                'message' => $result->getMessage()
            ]);
        }
        catch (\Exception $ex)
        {
            return self::json()->failure([
                'message' => $ex->getMessage()
            ]);
        }
    }
    
    public function uninstallAction ()
    {
        $name = self::request()->getParam('name');
        
        try
        {
            $result = self::plugins()->uninstall($name);
    
            if ($result->isSuccess()) {
                if ($result->hasJobs()) {
                    $output = new BufferedOutput();
                    $runner = new JobRunner($output);
                    $runner->run($result->getJobs());
        
                    self::json()->assign('jobOutput', $output->fetch());
                }
                
                return self::json()->success();
            }
    
            return self::json()->failure([
                'message' => $result->getMessage()
            ]);
        }
        catch (\Exception $ex)
        {
            return self::json()->failure([
                'message' => $ex->getMessage()
            ]);
        }
    }
    
    public function updateAction ()
    {
        $name = self::request()->getParam('name');
        
        try
        {
            $plugin = self::plugins()->get($name);
            
            if ($update = self::plugins()->getUpdater()->checkForUpdate($plugin->getInstance()))
            {
                $filename = $update->download();
                
                if ($update->extract($filename))
                {
                    self::plugins()->resetInstance($name);
                }
                else
                {
                    return self::json()->failure([
                        'Unable to extract plugin zip.'
                    ]);
                }
            }
            
            $result = self::plugins()->update($name);
    
            if ($result->isSuccess()) {
                if ($result->hasJobs()) {
                    $output = new BufferedOutput();
                    $runner = new JobRunner($output);
                    $runner->run($result->getJobs());
        
                    self::json()->assign('jobOutput', $output->fetch());
                }
                
                return self::json()->success([
                    'version' => $plugin->getInfo()->getVersion()
                ]);
            }
    
            return self::json()->failure([
                'message' => $result->getMessage()
            ]);
        }
        catch (\Exception $ex)
        {
            return self::json()->failure([
                'message' => $ex->getMessage()
            ]);
        }
    }
    
}