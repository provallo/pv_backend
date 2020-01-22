<?php

namespace ProVallo\Plugins\Backend\Job;

use ProVallo\Components\Job\JobInterface;
use ProVallo\Core;
use ProVallo\Plugins\Backend\Bootstrap;
use ProVallo\Plugins\Backend\Components\Config\Manager;
use Symfony\Component\Console\Output\OutputInterface;

class CreateConfigJob implements JobInterface
{
    
    public function getName (): string
    {
        return 'backend:create:config';
    }
    
    public function execute (OutputInterface $output): int
    {
        $output->writeln('Creating configuration');
        
        $config = new Manager();
        $plugin = Core::plugins()->get('Backend');
        
        $config->create($plugin, [
            'node.path' => [
                'type'  => 'text',
                'label' => 'node.js executable path',
                'value' => ''
            ]
        ]);
        
        return 0;
    }
    
}