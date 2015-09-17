<?php

require(__DIR__ . "/src/vendor/autoload.php");

use Symfony\Component\Console\Application;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application();

$console
    ->register('append')
    ->setDefinition([
        new InputOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output file name. If not not found the file will be created'),
        new InputArgument('input', InputArgument::IS_ARRAY, 'Directory, Glob or Array of csv\'s'),
    ])
    ->setDescription('Append csv\'s with the same structure together')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $output = $input->getOption('output');
        $csv_match = $input->getArgument('input');

        if(!count($csv_match)){
            throw new InvalidArgumentException("No input csv's specified");
        } elseif(count($csv_match) == 1) {
            // Determine if glob or dir
            if(file_exists($csv_match[1])){
                // it's a directory
            } else {
                // it's a glob
            }
        } else {
            // there's a series of files passed in, we can concat them individually
        }

        var_dump($csv_match);die();


//        $output->writeln(sprintf('Dir listing for <info>%s</info>', $dir));
    })
;

$console->run();