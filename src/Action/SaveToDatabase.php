<?php namespace TheHarvester\CsvUtil\Action;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use League\Csv\Reader;

class SaveToDatabase
{
    protected $reader;
    protected $table_name;
    protected $conn;

    public function __construct(Reader $reader, Connection $conn, $table_name)
    {
        $this->reader = $reader;
        $this->conn = $conn;
        $this->table_name = $table_name;
    }

    public function initTable()
    {
        $sm = $this->conn->getSchemaManager();
        if ($this->tableExists()) {
            $sm->dropTable($this->table_name);
        }
        $sm->createTable($this->getSchemaTable());
        $this->saveData();
    }

    public function saveData()
    {
        $query = $this->conn->createQueryBuilder();

        $column_names = array_map(function ($column_name) {
            return preg_replace("/[^a-zA-Z0-9]/", "_", $column_name);
        }, $this->reader->fetchOne());

        $this->reader->setOffset(1)->each(function ($row, $index, $iterator) use ($query, $column_names) {
            $query->insert($this->table_name);

            $query->setValue("csv_util_id", '?');
            foreach ($column_names as $key => $column_name) {
                $query->setValue("`$column_name`", '?');
            }
            $query->setParameter(0, $index);
            foreach ($row as $key => $value) {
                $query->setParameter($key + 1, $value);
            }
            $query->execute();
            return true;
        });
    }

    public function tableExists()
    {
        $sm = $this->conn->getSchemaManager();

        $table_found = false;
        /** @var Table $table */
        foreach ($sm->listTables() as $table) {
            if ($table->getName() == $this->table_name) {
                $table_found = true;
            }
        }
        return $table_found;
    }

    // For now this will just create a new table, we will extend eventually
    public function getSchemaTable()
    {
        $summary = (new ColumnSummary($this->reader))->getSummary();
        $columns = [new Column("csv_util_id", Type::getType(Type::INTEGER), ["unsigned" => 200])];
        foreach ($summary->getRowNames() as $column_name) {
            $column_name = $this->normalizeColumnName($column_name);
            $columns[] = (new Column($column_name, Type::getType(Type::STRING), ["length" => 200]))->setNotnull(false);
        }
        $table = new Table($this->table_name, $columns);
        $table->setPrimaryKey(['csv_util_id']);
        return $table;
    }

    public function normalizeColumnName($column_name)
    {
        return preg_replace("/[^a-zA-Z0-9]/", "_", $column_name);
    }
}