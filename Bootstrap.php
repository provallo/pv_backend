<?php

namespace ProVallo\Plugins\Backend;

use ProVallo\Core;
use ProVallo\Plugins\Backend\Commands\BackendBuildCommand;
use ProVallo\Plugins\Backend\Commands\BackendRegisterCommand;
use ProVallo\Plugins\Backend\Components\Auth;
use ProVallo\Plugins\Backend\Components\Config\Manager;
use ProVallo\Plugins\Backend\Components\ModelValidator;

class Bootstrap extends \ProVallo\Components\Plugin\Bootstrap
{
    
    public function install ()
    {
        $this->installDB();
        
        return true;
    }
    
    public function update ($previousVersion)
    {
        $this->installDB();
        
        return true;
    }
    
    public function execute()
    {
        if (Core::instance()->getApi() === Core::API_WEB)
        {
            // Register custom services
            Core::di()->registerShared('auth', function() {
                return new Auth();
            });
            
            Core::events()->subscribe('core.route.register', function() {
                Core::instance()->registerModule('backend', [
                    'controller' => [
                        'namespace'     => 'ProVallo\\Controllers\\Backend\\',
                        'class_suffix'  => 'Controller',
                        'method_suffix' => 'Action'
                    ]
                ]);
    
                // Redirect "/backend" to "/backend/"
                Core::instance()->get('/backend', function ($request, $response) {
                    return $response->withRedirect('/backend/');
                });
    
                // Define the default backend path
                Core::instance()->get('/backend/', 'backend:Index:index');
    
                // Register all backend controllers
                $this->registerController('Backend', 'Index');
                $this->registerController('Backend', 'User');
                $this->registerController('Backend', 'Config');
                $this->registerController('Backend', 'Group');
            });
        }
        
        if (Core::instance()->getApi() === CORE::API_CONSOLE)
        {
            // Register custom commands
            Core::events()->subscribe('console.register', function () {
                return [
                    new BackendRegisterCommand(),
                    new BackendBuildCommand()
                ];
            });
        }
    
        Core::di()->registerShared('backend.config', function() {
            return new Manager();
        });
        
        Core::di()->registerShared('modelValidator', function() {
            return new ModelValidator();
        });
    }

}