<?php namespace TheHarvester\CsvUtil\Command;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use League\Csv\Reader;
use PDO;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheHarvester\CsvUtil\Action\SaveToDatabase;

class SaveToMysqlCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('save:mysql')
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
                'The host name of the database server. Defaults to localhost',
                'localhost'
            )
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_OPTIONAL,
                'The username of the database connection. Defaults to root',
                'root'
            )->addOption(
                'password',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The password of the database connection. Defaults to (empty)',
                ''
            );

        $this->configureBase();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = $this->glob($input->getArgument('glob'));
        if (!count($paths)) {
            $output->write("No csvs found matching input path/glob");
            return;
        }

        $connection_params = array(
            'dbname' => $input->getArgument('database'),
            'user' => $input->getOption('user'),
            'password' => $input->getOption('password'),
            'host' => $input->getOption('host'),
            'driver' => 'pdo_mysql',
        );
        $config = new Configuration();
        $conn = DriverManager::getConnection($connection_params, $config);

        foreach ($paths as $path) {
            $reader = $this->getReader($input, $path);
            $table_name = preg_replace("/[^a-zA-Z0-9]/", "_", basename($path));
            $saver = new SaveToDatabase($reader, $conn, $table_name);
            $saver->initTable();
        }
    }
}