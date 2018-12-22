<?php

namespace ProVallo\Plugins\Backend\Commands;

use ProVallo\Components\Command;
use ProVallo\Core;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackendBuildCommand extends Command
{
    
    protected function configure ()
    {
        $this->setName('backend:build');
        $this->setDescription('Builds the backend.');
    }
    
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $directory = __DIR__ . '/../Views/backend';
        
        `cd $directory && yarn build`;
    }
    
}