<?php namespace TheHarvester\CsvUtil\Command;

use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class AbstractCommand extends Command
{
    public function configureBase()
    {
        $this->addOption(
            'limit',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Limits the amount of rows to be read from a csv'
        );
        $this->addOption(
            'delimiter',
            'd',
            InputOption::VALUE_OPTIONAL,
            'Sets the delimiter character of the csv. The default is ,'
        );
        $this->addOption(
            'enclosure',
            'e',
            InputOption::VALUE_OPTIONAL,
            'Sets the enclosure character of the cell. The defaults is "'
        );
        $this->addOption(
            'escape',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Sets the escape character. The default is \\'
        );
        return $this;
    }

    /**
     * Initializes the reader with options set
     * @param InputInterface $input
     * @param $path
     * @return Reader
     */
    protected function getReader(InputInterface $input, $path)
    {
        $reader = Reader::createFromPath($path);
        if ($input->getOption("delimiter")) {
            $reader->setDelimiter($input->getOption("delimiter"));
        }
        if ($input->getOption("enclosure")) {
            $reader->setEnclosure($input->getOption("enclosure"));
        }
        if ($input->getOption("escape")) {
            $reader->setEscape($input->getOption("escape"));
        }
        return $reader;
    }

    /**
     * Checks if the path is a directory and turns it into a glob string before running it through glob
     * @param $path
     * @return array
     */
    public function glob($path)
    {
        if (is_dir($path)) {
            $path = rtrim($path, "/") . "/*";
        }
        return glob($path);
    }

    public function getFileNameFromPath($path)
    {
        // TODO
        return $path;
    }
}