<?php namespace TheHarvester\CsvUtil\Action;

use League\Csv\Reader;

class HeaderInfoAction implements CsvFileAction
{
    /**
     * @inheritdoc
     */
    public function execute($path)
    {
        $csv = Reader::createFromPath($path);
        return $csv->fetchOne();
    }
}