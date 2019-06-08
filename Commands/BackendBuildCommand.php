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
        
        if (!is_dir($directory . '/node_modules'))
        {
            $output->writeln('Missing "node_modules" in ' . $directory);
            $output->writeln('Please install missing dependencies by using yarn or npm.');
            return;
        }
        
        `cd $directory && yarn build`;
    }
    
}