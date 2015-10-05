<?php namespace TheHarvester\CsvUtil\Command;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SelfUpdateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('self-update')
            ->setDescription('Updates Csv Util to the latest version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = new Manager(Manifest::loadFile(
            'http://theharvester.github.io/csv-util/manifest.json'
        ));

        // update to the next available 1.x update
        $manager->update('1.0.0', true);
    }
}