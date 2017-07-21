<?php

namespace SinSquare\Bundle\Tests\BundleTest\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setName('test:command')
            ->setDescription('Test command.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var TestBootApplication $silex */
        $silex = $this->getSilexApplication();
        $output->write($silex->isBooted() ? 'Booted' : 'Not booted');
    }
}
