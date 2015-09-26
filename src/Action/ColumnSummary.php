<?php namespace TheHarvester\CsvUtil\Action;

use League\Csv\Reader;
use TheHarvester\CsvUtil\Helper\OutputTable;

/**
 * Class ColumnDetailAction
 * Gives a break down of the csv data
 * @package TheHarvester\CsvUtil\Action
 */
class ColumnSummary
{
    /** @var \League\Csv\Reader $reader */
    protected $reader;

    const COLUMN_NAME = 'Column name';
    const MIN_STRING_LENGTH = 'Min string length';
    const MAX_STRING_LENGTH = 'Max string length';
    const IS_INTEGER_ONLY = 'Integer';
    const IS_NUMERIC_ONLY = 'Numeric';
    const IS_BINARY_ONLY = 'Binary bool';
    const IS_STRING_BOOL = 'String bool';
    const IS_EMPTY = 'Empty';

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function prepareOutputTable()
    {
        $table = new OutputTable();
        $table->setRowNames($this->reader->fetchOne());
        $table->setColumnNames([
            self::COLUMN_NAME,
            self::MIN_STRING_LENGTH,
            self::MAX_STRING_LENGTH,
            self::IS_INTEGER_ONLY,
            self::IS_NUMERIC_ONLY,
            self::IS_BINARY_ONLY,
            self::IS_STRING_BOOL,
            self::IS_EMPTY,
        ]);

        foreach ($table->getRowNames() as $column_name) {
            $table->setValue($column_name, self::COLUMN_NAME, $column_name);
            $table->setValue($column_name, self::MIN_STRING_LENGTH, 9999);
            $table->setValue($column_name, self::MAX_STRING_LENGTH, 0);
            $table->setValue($column_name, self::IS_INTEGER_ONLY, true);
            $table->setValue($column_name, self::IS_NUMERIC_ONLY, true);
            $table->setValue($column_name, self::IS_BINARY_ONLY, true);
            $table->setValue($column_name, self::IS_STRING_BOOL, true);
            $table->setValue($column_name, self::IS_EMPTY, true);
        }
        return $table;
    }

    public function getSummary()
    {
        $table = $this->prepareOutputTable();

        $this->reader->setOffset(1)->each(function ($row, $index, $iterator) use (&$table) {
            foreach ($row as $row_id => $value) {
                // Here's the magic
                $str_len = strlen($value);
                if ($str_len < $table->getValue($row_id, self::MIN_STRING_LENGTH)) {
                    $table->setValue($row_id, self::MIN_STRING_LENGTH, $str_len);
                }

                if ($str_len > $table->getValue($row_id, self::MAX_STRING_LENGTH)) {
                    $table->setValue($row_id, self::MAX_STRING_LENGTH, $str_len);
                }

                if (!ctype_digit($value)) {
                    $table->setValue($row_id, self::IS_INTEGER_ONLY, false);
                }

                if (!is_numeric($value)) {
                    $table->setValue($row_id, self::IS_NUMERIC_ONLY, false);
                }

                if (!in_array($value, ['0', '1'])) {
                    $table->setValue($row_id, self::IS_BINARY_ONLY, false);
                }

                if (!in_array(strtolower($value), ['true', 'false'])) {
                    $table->setValue($row_id, self::IS_STRING_BOOL, false);
                }

                if ($str_len) {
                    $table->setValue($row_id, self::IS_EMPTY, false);
                }
            }
            return true;
        });

        return $table;
    }
}