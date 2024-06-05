<?php

namespace Er1z\MarlinConfigConverter\Command;

use Er1z\MarlinConfigConverter\Action\GenerateConfigAction;
use Er1z\MarlinConfigConverter\Symfony\IniEncoder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class ConvertCommand extends Command
{
    public function __construct(
        private readonly GenerateConfigAction $generateConfigAction,
        private readonly EncoderInterface $encoder
    ) {
        parent::__construct('convert');
    }

    protected function configure(): void
    {
        $this->addArgument(
            'configuration',
            InputArgument::REQUIRED,
            'Path to Configuration.h file'
        );
        $this->addOption(
            'configuration-adv',
            null,
            InputOption::VALUE_REQUIRED,
            'Path to Configuration_adv.h'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->writeToStdErr('Converting configuration', $output);

        $configuration = ($this->generateConfigAction)(
            $input->getArgument('configuration'),
            $input->getOption('configuration-adv')
        );
        $configuration->ksort();

        $this->writeToStdErr('Dumping configuration', $output);

        $output->writeln(
            $this->encoder->encode($configuration->getArrayCopy(), IniEncoder::FORMAT, [IniEncoder::PAD_KEYS => 1])
        );

        $this->writeToStdErr('Done', $output);

        return 0;
    }

    protected function writeToStdErr(string $msg, OutputInterface $output): void
    {
        $target = $output;
        if ($output instanceof ConsoleOutputInterface) {
            $target = $output->getErrorOutput();
        }

        $target->writeln($msg);
    }
}
