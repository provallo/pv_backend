<?php

namespace ProVallo\Plugins\Backend\Job;

use ProVallo\Components\Job\JobInterface;
use ProVallo\Plugins\Backend\Bootstrap;
use Symfony\Component\Console\Output\OutputInterface;

class BuildJob implements JobInterface
{
    
    public function getName (): string
    {
        return 'backend:build';
    }
    
    public function execute (OutputInterface $output): int
    {
        $directory = __DIR__ . '/../Views/backend';
        
        if (!is_dir($directory . '/node_modules'))
        {
            $output->writeln('Missing "node_modules" in ' . $directory);
            $output->writeln('Please install missing dependencies by using yarn or npm.');
            
            return 1;
        }
        
        $config  = Bootstrap::getConfig();
        $nodeBin = $config['node.path'];
        
        if (empty($nodeBin) || !is_file($nodeBin) || !is_readable($nodeBin) || !is_executable($nodeBin))
        {
            $output->writeln('Invalid node bin path.');
            
            return 1;
        }
        
        $path    = dirname($nodeBin);
        $command = "export PATH=$path; cd $directory && yarn build 2>&1";
        
        exec($command, $stdout, $returnCode);
        
        $stdout = implode(PHP_EOL, $stdout);
        $stdout = preg_replace('/\[([0-9]+)m/m', '', $stdout);
        
        $output->writeln($stdout);
        
        return $returnCode;
    }
    
}