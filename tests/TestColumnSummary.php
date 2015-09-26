<?php namespace CsvUtil\Tests;

use League\Csv\Reader;
use TheHarvester\CsvUtil\Action\ColumnSummary;

class TestColumnSummary extends TestCase
{
    public function test_prepares_out_table_correctly()
    {
        $detail = new ColumnSummary(Reader::createFromPath(__DIR__ . "/samples/col_summary.csv"));
        $table = $detail->prepareOutputTable();

        $this->assertEquals([
            ColumnSummary::COLUMN_NAME,
            ColumnSummary::MIN_STRING_LENGTH,
            ColumnSummary::MAX_STRING_LENGTH,
            ColumnSummary::IS_INTEGER_ONLY,
            ColumnSummary::IS_NUMERIC_ONLY,
            ColumnSummary::IS_BINARY_ONLY,
            ColumnSummary::IS_STRING_BOOL,
            ColumnSummary::IS_EMPTY,
        ], $table->getColumnNames());

        $this->assertEquals([
            'Min Max',
            'Integers',
            'Numbers',
            'Binary',
            'String Bool',
            'Is Empty',
        ], $table->getRowNames());
    }

    public function test_gets_summary_from_table()
    {
        $detail = new ColumnSummary(Reader::createFromPath(__DIR__ . "/samples/col_summary.csv"));
        $table = $detail->getSummary();

        // This could be tested better
        $this->assertEquals(0,$table->getValue('Min Max', ColumnSummary::MIN_STRING_LENGTH));
        $this->assertEquals(7,$table->getValue('Min Max', ColumnSummary::MAX_STRING_LENGTH));

        $this->assertEquals(1,$table->getValue('Integers', ColumnSummary::IS_INTEGER_ONLY));
        $this->assertEquals(1,$table->getValue('Integers', ColumnSummary::IS_NUMERIC_ONLY));

        $this->assertFalse($table->getValue('Numbers', ColumnSummary::IS_INTEGER_ONLY));
        $this->assertEquals(1,$table->getValue('Numbers', ColumnSummary::IS_NUMERIC_ONLY));

        $this->assertEquals(1,$table->getValue('Binary', ColumnSummary::IS_BINARY_ONLY));

        $this->assertEquals(1,$table->getValue('String Bool', ColumnSummary::IS_STRING_BOOL));

        $this->assertEquals(1,$table->getValue('Is Empty', ColumnSummary::IS_EMPTY));
    }
}