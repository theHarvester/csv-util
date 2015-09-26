<?php namespace TheHarvester\CsvUtil\Helper;

use TheHarvester\CsvUtil\Exception\NotFoundException;

class OutputTable
{
    /** @var string[] $column_names */
    protected $column_names;
    /** @var string[] $row_names */
    protected $row_names;
    /**
     * @var array[] $data Data stored by row and then column
     *   00 01 02
     *   10 11 12
     *   20 21 22
     */
    protected $data;
    /** @var int $max_column_id A small variable so we know how far to fill in the rows */
    protected $max_column_id;

    public function __construct()
    {
        $this->data = [];
        $this->setColumnNames([]);
        $this->setRowNames([]);
        $this->max_column_id = 0;
    }

    /**
     * @return \string[]|null
     */
    public function getColumnNames()
    {
        return $this->column_names;
    }

    /**
     * @param \string[] $column_names
     * @return $this
     */
    public function setColumnNames($column_names)
    {
        $this->column_names = $column_names;
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getRowNames()
    {
        return $this->row_names;
    }

    /**
     * @param string[] $row_names
     * @return $this
     */
    public function setRowNames($row_names)
    {
        $this->row_names = $row_names;
        return $this;
    }

    /**
     * @param string|int $row_name Gets row id by row name or falls back to id if name can't be found
     * @return int
     * @throws NotFoundException
     */
    public function getRowId($row_name)
    {
        if (in_array($row_name, $this->getRowNames())) {
            return array_search($row_name, $this->getRowNames());
        }

        if (!is_numeric($row_name)) {
            throw new NotFoundException("Could not find the row name $row_name");
        }

        return $row_name;
    }

    /**
     * @param string|int $column_name Gets column id by row name or falls back to id if name can't be found
     * @return int
     * @throws NotFoundException
     */
    public function getColumnId($column_name)
    {
        if (in_array($column_name, $this->getColumnNames())) {
            return array_search($column_name, $this->getColumnNames());
        }

        if (!is_numeric($column_name)) {
            throw new NotFoundException("Could not find the row name $column_name");
        }

        return $column_name;
    }

    public function getValue($row_name, $column_name)
    {
        $row_id = $this->getRowId($row_name);
        $column_id = $this->getColumnId($column_name);

        if (!isset($this->data[$row_id])) {
            return null;
        }
        if (!isset($this->data[$row_id][$column_id])) {
            return null;
        }
        return $this->data[$row_id][$column_id];
    }

    public function setValue($row_name, $column_name, $value)
    {
        $row_id = $this->getRowId($row_name);
        $column_id = $this->getColumnId($column_name);

        if (!isset($this->data[$row_id])) {
            $this->data[$row_id] = [];
        }
        $this->data[$row_id][$column_id] = $value;

        // Set this for when we convert to array
        if ($column_id > $this->max_column_id) {
            $this->max_column_id = $column_id;
        }
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function toArray()
    {
        $output = [];
        $data = $this->data;
        // Fill the misssing rows with empty arrays
        for ($i = 0; $i < max(array_keys($data)); $i++) {
            if (!isset($data[$i])) {
                $data[$i] = [];
            }
        }
        ksort($data);

        // loop the data and array fill the columns and then return
        foreach ($data as $row_id => $row_data) {
            // Fill the missing column values
            for ($i = 0; $i <= $this->max_column_id; $i++) {
                if (!isset($row_data[$i])) {
                    $row_data[$i] = null;
                }
            }
            ksort($row_data);
            $data[$row_id] = $row_data;
        }
        return $data;
    }
}