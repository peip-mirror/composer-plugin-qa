<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\CopyPasteDetector;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Webs\QA\Command\Util;

/**
 * Copy/Paste Detector
 */
class CopyPasteDetector extends BaseCommand
{
    /**
     * Console description
     *
     * @var string
     */
    protected $description = 'Copy/Paste Detector';

    /**
     * Console params configuration
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('qa:copy-paste-detector')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of directories/files to search <comment>[Default:"src,app,tests"]</>'
            )
            ->addOption(
                'diff',
                null,
                InputOption::VALUE_NONE,
                'Use `git status -s` to search files to check'
            );
    }

    /**
     * Execution
     *
     * @param  InputInterface  $input  Input console
     * @param  OutputInterface $output Output console
     * @return integer                 Exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->output = $output;
        $style = new SymfonyStyle($input, $output);
        $style->title($this->description);

        $util = new Util();
        $cpd = $util->checkBinary('phpcpd');
        $source = $util->checkSource($input);
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        if (empty($source)) {
            $output->writeln('<error>No files found</>');
            $style->newLine();

            return 1;
        }

        $cmd = $cpd.' '.$source.' --ansi --fuzzy';
        $output->writeln('<info>Command: '.$cmd.'</>');
        $style->newLine();
        $process = new Process($cmd);
        $process->run();
        $output->writeln($process->getOutput());
        $end = microtime(true);
        $time = round($end - $start);

        $style->section('Results');
        $output->writeln('<info>Time: '.$time.' seconds</>');
        $style->newLine();

        return $process->getExitCode();
    }
}
