<?php

namespace ProVallo\Plugins\Backend;

use ProVallo\Components\Plugin\LifecycleResult;
use ProVallo\Core;
use ProVallo\Plugins\Backend\Commands\BackendBuildCommand;
use ProVallo\Plugins\Backend\Commands\BackendRegisterCommand;
use ProVallo\Plugins\Backend\Components\Auth;
use ProVallo\Plugins\Backend\Components\Config\Manager;
use ProVallo\Plugins\Backend\Components\ModelValidator;
use ProVallo\Plugins\Backend\Components\Permission\PermissionService;
use ProVallo\Plugins\Backend\Job\BuildJob;

class Bootstrap extends \ProVallo\Components\Plugin\Bootstrap
{
    
    public function install ()
    {
        $this->installDB();
        $this->createConfig();
        
        return (new LifecycleResult(LifecycleResult::TYPE_INSTALL, true))
            ->addJob(new BuildJob());
    }
    
    public function update ($previousVersion)
    {
        $this->installDB();
        $this->createConfig();
        
        $permission = new PermissionService();
        $permission->add('user.backend.access', true, 'Allow the user to login into backend.');
        
        return (new LifecycleResult(LifecycleResult::TYPE_UPDATE, true))
            ->addJob(new BuildJob());
    }
    
    public function createConfig ()
    {
        Core::di()->get('backend.config')->create($this, [
            'node.path' => [
                'type'  => 'text',
                'label' => 'node.js executable path',
                'value' => ''
            ]
        ]);
    }
    
    public function execute ()
    {
        if (Core::instance()->getApi() === Core::API_WEB)
        {
            // Register custom services
            Core::di()->registerShared('auth', function ()
            {
                return new Auth();
            });
            
            Core::di()->registerShared('permission', function ()
            {
                return new PermissionService();
            });
            
            Core::events()->subscribe('core.route.register', function ()
            {
                Core::instance()->registerModule('backend', [
                    'controller' => [
                        'namespace'     => 'ProVallo\\Controllers\\Backend\\',
                        'class_suffix'  => 'Controller',
                        'method_suffix' => 'Action'
                    ]
                ]);
                
                // Redirect "/backend" to "/backend/"
                Core::instance()->get('/backend', function ($request, $response)
                {
                    return $response->withRedirect('/backend/');
                });
                
                // Define the default backend path
                Core::instance()->get('/backend/', 'backend:Index:index');
                
                // Register all backend controllers
                $this->registerController('Backend', 'Index');
                $this->registerController('Backend', 'User');
                $this->registerController('Backend', 'Config');
                $this->registerController('Backend', 'Group');
                $this->registerController('Backend', 'Permission');
                $this->registerController('Backend', 'Plugin');
            });
        }
        
        if (Core::instance()->getApi() === CORE::API_CONSOLE)
        {
            // Register custom commands
            Core::events()->subscribe('console.register', function ()
            {
                return [
                    new BackendRegisterCommand(),
                    new BackendBuildCommand()
                ];
            });
        }
        
        Core::di()->registerShared('backend.config', function ()
        {
            return new Manager();
        });
        
        Core::di()->registerShared('modelValidator', function ()
        {
            return new ModelValidator();
        });
    }
    
    public static function getConfig ()
    {
        $plugin = Core::plugins()->get('Backend');
        $config = Core::di()->get('backend.config')->get($plugin);
        
        return $config;
    }
    
}