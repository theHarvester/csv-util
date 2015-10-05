<?php namespace TheHarvester\CsvUtil\Command;

use League\Csv\Reader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheHarvester\CsvUtil\Action\ColumnSummary;
use TheHarvester\CsvUtil\Action\PreviewRows;

class PreviewCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('header:preview')
            ->setDescription('Shows a preview of the csv(s).')
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
        if (!count($paths)) {
            $output->write("No csvs found matching input glob");
            return;
        }

        foreach ($paths as $path) {
            $detail = new PreviewRows($this->getReader($input, $path));
            $table = $detail->getSummary();

            $this->getHelper('table')
                ->setHeaders($table->getColumnNames())
                ->setRows($table->toArray())
                ->render($output);
        }
    }
}