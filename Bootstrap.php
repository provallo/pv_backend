<?php

namespace ProVallo\Plugins\Backend;

use ProVallo\Core;
use ProVallo\Plugins\Backend\Commands\BackendBuildCommand;
use ProVallo\Plugins\Backend\Commands\BackendRegisterCommand;
use ProVallo\Plugins\Backend\Components\Auth;
use ProVallo\Plugins\Backend\Components\ModelValidator;

class Bootstrap extends \ProVallo\Components\Plugin\Bootstrap
{

    public function execute()
    {
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
        
        // Register custom services
        Core::di()->registerShared('auth', function() {
            return new Auth();
        });
    
        Core::di()->registerShared('modelValidator', function() {
            return new ModelValidator();
        });
        
        // Register custom commands
        Core::events()->subscribe('console.register', function () {
            return [
                new BackendRegisterCommand(),
                new BackendBuildCommand()
            ];
        });
    }

}