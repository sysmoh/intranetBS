<?php

namespace AppBundle\Command;

/* Specifics class for command */
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
/* Filesystem */
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;


class TestsCommand extends ContainerAwareCommand
{

    private $output;
    private $input;

    protected function configure()
    {
        $this
            ->setName('tests')
            ->setDescription('Tests sur l\'application')
            ->addArgument('group', InputArgument::OPTIONAL, 'group:groupeName')
            ->addOption('log',null,InputOption::VALUE_NONE,'log messages in file: app/logs/TestsCommand.log')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;

        $groupArg = $this->input->getArgument('group');
        $group = null;
        if($groupArg != null)
        {
            $group = explode('group:',$groupArg)[1];
        }

        //clear cache of env test
        $cache = shell_exec("php app/console cache:clear --env=test --no-debug");
        $this->output($cache,'info');

        $startInfo = 'Starting tests';
        $shell_command = "php /usr/local/bin/phpunit -d memory_limit=-1 -c app";
        /* l'option "-d memory_limit=-1" permet de modifier la limite mémoire du script phpunit. */

        if($group != null)
        {
            $startInfo = $startInfo.' : group: '.$group;
            $shell_command = "php /usr/local/bin/phpunit --group ".$group." --verbose -d memory_limit=-1 -c app";
            /* l'option "-d memory_limit=-1" permet de modifier la limite mémoire du script phpunit. */
        }

        $this->output($startInfo,'info');
        $version = shell_exec("php /usr/local/bin/phpunit --version");
        $this->output($version,'info');



        $message = shell_exec($shell_command);
        $this->output($message,'comment');


        if ($input->getOption('log')) {
            $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
            $logFile = $rootDir.'/logs/TestsCommand.log';
            $fs = new Filesystem();
            $fs->dumpFile($logFile,$message);
        }



        $pattern = '/OK/';
        $ok = preg_match($pattern,$message);
        $pattern = '/FAILURES/';
        $failures = preg_match($pattern,$message);
        if($ok && !$failures)
        {
            $this->output('Test passed!!!','info');
        }
        else
        {
            $this->output('Error in tests...','error');
        }

        $this->output('Finish tests','info');





    }


    private function output($string,$mode = null){

        if (OutputInterface::VERBOSITY_VERBOSE <= $this->output->getVerbosity()) {

            switch($mode){
                case null;
                    break;
                case 'error':
                    $string = '<error>Error:</error> '.$string;
                    break;
                case 'info':
                    $string = '<info>Info:</info> '.$string;
                    break;
                case 'comment':
                    $string = '<comment>Comment:</comment> '.$string;
                    break;
            }
            $this->output->writeln(PHP_EOL.$string);
        }
    }

}

