<?php namespace CsvUtil\Tests\Helper;

use CsvUtil\Tests\TestCase;
use TheHarvester\CsvUtil\Exception\NotFoundException;
use TheHarvester\CsvUtil\Helper\OutputTable;

class TestOutputTable extends TestCase
{
    public function test_does_store_column_names_correctly()
    {
        $column_names = ["foo", "bar", "baz"];
        $table = new OutputTable();
        $table->setColumnNames($column_names);
        $this->assertEquals($column_names, $table->getColumnNames());
        $this->assertEquals(1, $table->getColumnId("bar"));
        $this->assertEquals(5, $table->getColumnId(5));

        try {
            $table->getColumnId("not_in_col_names");
            $this->fail();
        } catch (NotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    public function test_can_set_row_value_by_column_name()
    {
        $row_names = ["foo", "bar", "baz"];
        $table = new OutputTable();
        $table->setRowNames($row_names);

        $this->assertEquals($row_names, $table->getRowNames());
        $this->assertEquals(1, $table->getRowId("bar"));
        $this->assertEquals(5, $table->getRowId(5));

        try {
            $table->getRowId("not_in_col_names");
            $this->fail();
        } catch (NotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    public function test_can_set_data_correctly()
    {
        $table = new OutputTable();
        $table->setValue(0, 1, "test");
        $this->assertEquals([0 => [1 => "test"]], $table->getData());

    }

    public function test_can_set_data_by_named_rows_and_columns()
    {
        $table = new OutputTable();
        $table->setRowNames(["row1", "row2"]);
        $table->setColumnNames(["col1", "col2"]);
        $table->setValue("row2", "col2", "test");
        $this->assertEquals([1 => [1 => "test"]], $table->getData());
        $this->assertEquals(
            [
                0 => [
                    0 => null,
                    1 => null,
                ],
                1 => [
                    0 => null,
                    1 => "test"
                ]
            ],
            $table->toArray()
        );
    }
}