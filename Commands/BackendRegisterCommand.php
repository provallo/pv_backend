<?php

namespace ProVallo\Plugins\Backend\Commands;

use ProVallo\Components\Command;
use ProVallo\Core;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackendRegisterCommand extends Command
{
    
    protected function configure ()
    {
        $this->setName('backend:register');
        $this->setDescription('Linking additional backend components.');
    }
    
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Collecting components...');
        
        /** @var \ProVallo\Components\Plugin\Bootstrap[] $plugins */
        $plugins = Core::events()->collect('backend.register');
        
        foreach ($plugins as $plugin)
        {
            $name        = $plugin->getInfo()->toArray()['name'];
            $source      = path($plugin->getPath(), 'Views', 'backend');
            $destination = path(__DIR__, '../Views/backend/src/vendor/' . $name);
            
            if (is_link($destination))
            {
                unlink($destination);
            }
    
            symlink($source, $destination);
            
            $output->writeln('@' . $name . ' => ' . $source);
        }
    }
    
}