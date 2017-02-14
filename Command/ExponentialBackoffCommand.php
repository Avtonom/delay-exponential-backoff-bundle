<?php

namespace Avtonom\ExponentialBackoffBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\Table;

class ExponentialBackoffCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('exponential-backoff')
            ->setDescription('Exponential backoff to delay')
            ->addArgument('algorithm', InputArgument::OPTIONAL, 'algorithm to delay', 'halfDelay')
            ->addOption('length', 'l', InputOption::VALUE_OPTIONAL, 'length', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('algorithm: '.$input->getArgument('algorithm'));
        $exponentialBackoff = $this->getContainer()->get('avtonom_exponential_backoff');

        $table = new Table($output);
        $table->setHeaders(array('attempt', 'microseconds', 'seconds', 'minutes', 'hours'));

        $length = $input->getOption('length') ? : 10;
        for($i=1; $i < $length; $i ++){
            $time = call_user_func([$exponentialBackoff, $input->getArgument('algorithm')], $i);
            $table->addRow([
                $i,
                $time,
                (int) floor($time / 1000000),
                round($time / 60000000, 2),
                round($time / 3600000000, 2),
            ]);
        }
        $table->render();
    }
}