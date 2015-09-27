<?php namespace TheHarvester\CsvUtil\Action;

use League\Csv\Reader;
use TheHarvester\CsvUtil\Helper\OutputTable;

/**
 * Class ColumnDetailAction
 * Analyzes a csv and gives a break down of the data typs
 * @package TheHarvester\CsvUtil\Action
 */
class PreviewRows
{
    /** @var \League\Csv\Reader $reader */
    protected $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Analyizes the csv and gets a summary of the columns
     * @return OutputTable
     */
    public function getSummary($limit = 10)
    {
        $table = new OutputTable();

        $table->setColumnNames($this->reader->fetchOne());

        $this->reader->setOffset(1)->each(function ($row, $index, $iterator) use (&$table, $limit) {
            foreach ($row as $row_id => $value) {
                $table->setValue($index-1, $row_id, $value);
            }
            return ($index < $limit);
        });

        return $table;
    }
}