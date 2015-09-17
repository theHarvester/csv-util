<?php namespace TheHarvester\CsvUtil\Action;

interface CsvFileAction
{
    /**
     * Executes the action on a given csv file. Each action should be standalone
     * @param string $path
     * @return array
     */
    public function execute($path);
}