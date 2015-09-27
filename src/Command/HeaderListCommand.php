<?php namespace TheHarvester\CsvUtil\Command;

use League\Csv\Reader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheHarvester\CsvUtil\Action\ColumnSummary;
use TheHarvester\CsvUtil\Action\HeaderInfoAction;
use TheHarvester\CsvUtil\Helper\OutputTable;

class HeaderListCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('header:list')
            ->setDescription('Lists the header row of the csv(s).')
            ->addArgument(
                'glob',
                InputArgument::REQUIRED,
                'The file path glob of the files to be summaried'
            );
        $this->configureBase();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = $this->glob($input->getArgument('glob'));
        if(!count($paths)) {
            $output->write("No csvs found matching input glob");
            return;
        }

        foreach($paths as $path){
            $names = $this->getReader($input, $path)->fetchOne();
            $csv_name = $this->getFileNameFromPath($path);
            $output->writeln("<info>Column details for file: $csv_name</info>");

            $table = (new OutputTable())
                ->setRowNames($names)
                ->setColumnNames(['Column Names']);

            foreach($names as $col_name){
                $table->setValue($col_name, 0, $col_name);
            }

            $this->getHelper('table')
                ->setHeaders($table->getColumnNames())
                ->setRows($table->toArray())
                ->render($output);
        }
    }
}