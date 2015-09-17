<?php namespace TheHarvester\CsvUtil\Command;

use League\Csv\Reader;
use PDO;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheHarvester\CsvUtil\Action\ColumnDetailAction;
use TheHarvester\CsvUtil\Action\HeaderInfoAction;

class CommitToDbCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('db:mysql')
            ->setDescription('Writes csvs to database tables. Clears tables if they already exists')
            ->addArgument(
                'glob',
                InputArgument::REQUIRED,
                'The file path glob of the files to be summaried'
            )
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'The name of the database'
            )
            ->addOption(
                'host',
                null,
                InputArgument::OPTIONAL,
                'The host name of the database server. Defaults to localhost'
            )
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_OPTIONAL,
                'The username of the database connection. Defaults to root'
            )->addOption(
                'password',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The password of the database connection. Defaults to (empty)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = $this->glob($input->getArgument('glob'));
        if (!count($paths)) {
            $output->write("No csvs found matching input path/glob");
            return;
        }
        $host = $input->getOption("host") ?: "localhost";
        $database = $input->getArgument("database");
        $pdo = new \PDO("mysql:host={$host};dbname={$database};charset=utf8", $input->getOption("user")?:'root', $input->getOption("password")?:'');

        $header_action = new HeaderInfoAction();
        foreach ($paths as $path) {
            $colums = array_map(function($name){
                return preg_replace("/[^a-zA-Z0-9]/", "_", $name);
            },$header_action->execute($path));

            $table_name = preg_replace("/[^a-zA-Z0-9]/", "_", basename($path));
            $output->writeln($table_name . " starting...");

            $output->writeln(" - Clearing tables");
            $this->deleteTable($pdo, $table_name);
            $output->writeln(" - Creating new tables");
            $this->createTable($pdo, $table_name, $colums);

            $sql = "INSERT INTO $table_name (" . implode(", ", $colums) . ") VALUES (" . implode(", ", array_map(function(){return "?";}, $colums)) . ")";

            $output->writeln(" - Inserting csv data");
            $csv = Reader::createFromPath($path);
            $csv->setOffset(1); //because we don't want to insert the header
            $sth = $pdo->prepare($sql);
            $csv->each(function ($row) use (&$sth, $table_name, $colums, $sql) {
                foreach($row as $col_id => $value){
                    $sth->bindValue($col_id+1, $value, PDO::PARAM_STR);
                }
                return $sth->execute(); //if the function return false then the iteration will stop
            });
            $output->writeln(" - Complete");
        }
    }

    public function deleteTable(PDO $pdo, $table_name)
    {
        $sth = $result = $pdo->prepare("SHOW tables like :name");
        $sth->bindValue(':name', $table_name, PDO::PARAM_STR);
        $sth->execute();

        if($sth->rowCount()){
            $pdo->query("DROP TABLE $table_name");
        }
    }

    public function createTable(PDO $pdo, $table_name, $colums)
    {
        $sql = "CREATE TABLE {$table_name} ( __id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, ";
        $column_sql = [];
        foreach($colums as $column){
            $column_sql[] = $column . " varchar(255)" ;
        }
        $sql .= implode(", ", $column_sql) . ");";
        $pdo->query($sql);
    }
}