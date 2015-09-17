<?php namespace TheHarvester\CsvUtil\Action;

use League\Csv\Reader;

/**
 * Class ColumnDetailAction
 * Gives a break down of what
 * @package TheHarvester\CsvUtil\Action
 */
class ColumnDetailAction implements CsvFileAction
{
    /**
     * @inheritdoc
     */
    public function execute($path)
    {
        $csv = Reader::createFromPath($path);
        // Init the header arrays
        $column_names = $csv->fetchOne();
        $column_details = array_map(function ($data) {
            return [
                'min' => 9999, // min string length
                'max' => 0, // max string length
                'is_int' => true,// [0-9]
                'is_numberic' => true, // passes is numeric in php
                'is_binary' => true, // null 0 or 1
                'is_str_bool' => true, // null true or false
                'is_empty' => true,
            ];
        }, $column_names);
        $csv->each(function ($row, $index, $iterator) use (&$column_details) {
            if ($index == 0) {
                return true;
            }
            foreach ($row as $col_id => $column) {
                // Here's the magic
                $details = $column_details[$col_id];

                $str_len = strlen($column);
                if ($str_len < $details['min']) {
                    $details['min'] = $str_len;
                }

                if ($str_len > $details['max']) {
                    $details['max'] = $str_len;
                }

                if (!ctype_digit($column)) {
                    $details['is_int'] = false;
                }

                if (!is_numeric($column)){
                    $details['is_numeric'] = false;
                }

                if ($column && ($column !== 0 || $column !== 1)) {
                    $details['is_binary'] = false;
                }

                if ($column && (strtolower($column) !== 'true' || strtolower($column) !== 'false')) {
                    $details['is_str_bool'] = false;
                }

                if (strlen($column)) {
                    $details['is_empty'] = false;
                }

                $column_details[$col_id] = $details;
            }

            if ($index > 5) {
                return false;
            }
            return true;
        });

        return $column_details;
    }
}