<?php namespace TheHarvester\CsvUtil\Command;

use League\Csv\Reader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TheHarvester\CsvUtil\Action\ColumnDetailAction;
use TheHarvester\CsvUtil\Action\HeaderInfoAction;

class HeaderSummaryCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('summary')
            ->setDescription('Prepares a summary of the csv(s).')
            ->addArgument(
                'glob',
                InputArgument::REQUIRED,
                'The file path glob of the files to be summaried'
            )
            ->addOption(
                'summary',
                's',
                InputOption::VALUE_NONE,
                'If set, the column summaries will be returned'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = $this->glob($input->getArgument('glob'));
        if(!count($paths)) {
            $output->write("No csvs found matching input glob");
            return;
        }

        foreach($paths as $path){
            $header_action = new HeaderInfoAction(Reader::createFromPath($path));
            $header_names = [];
            $header_names[$path] = array_map(function($header){
                return [$header];
            },$header_action->toArray($path));

            if($input->getOption("summary")){
                $detail = new ColumnDetailAction();
                $column_summaries = $detail->execute($path);

                foreach($header_names[$path] as $id => $name){
                    $header_names[$path][$id] = array_values(array_merge($name,$column_summaries[$id]));
                }
            }
        }

        foreach($header_names as $csv_name => $details) {
            $output->writeln("Column details for file: " . $csv_name);
            $table = $this->getHelper('table');
            $table
                ->setHeaders(
                    !$input->getOption("summary") ? ['Column Names'] : ['Column Names', 'Min Length', 'Max Length', 'Integer', 'Numeric', 'Bit Boolean', 'String Boolean', 'Empty']
                )
                ->setRows($details);
            $table->render($output);
        }
    }
}