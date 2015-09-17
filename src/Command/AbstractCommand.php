<?php namespace TheHarvester\CsvUtil\Command;

use Symfony\Component\Console\Command\Command;

class AbstractCommand extends Command
{
    /**
     * Checks if the path is a directory and turns it into a glob string before running it through glob
     * @param $path
     * @return array
     */
    public function glob($path)
    {
        if(is_dir($path)){
            $path = rtrim($path, "/");
            return glob($path . "/**");
        }
        return glob($path);
    }
}