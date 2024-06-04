<?php

namespace Er1z\MarlinConfigConverter;

use Er1z\MarlinConfigConverter\Exception\EmptyConfigurationException;
use Er1z\MarlinConfigConverter\Exception\PreprocessorNotCallableException;
use Symfony\Component\Process\Exception\ProcessStartFailedException;
use Symfony\Component\Process\Process;

final readonly class ConfigurationExtractor
{
    public function __construct(
        private string $cppPath = 'cpp',
        private string $cppMinionPath = __DIR__.'/../minion.cpp'
    ) {
    }

    public function __invoke(
        string $marlinDir,
        ?string $configurationPath = null,
        ?string $configurationAdvPath = null
    ): string {
        $this->checkPreprocessor();
        $this->patchInstallationWithCustomConfigurationFiles($marlinDir, $configurationPath, $configurationAdvPath);

        return $this->compileAndCaptureOutput($marlinDir);
    }

    private function patchInstallationWithCustomConfigurationFiles(
        string $marlinDir,
        ?string $configurationPath = null,
        ?string $configurationAdvPath = null
    ): void {
        if ($configurationPath) {
            copy($configurationPath, sprintf('%s/Marlin/Configuration.h', $marlinDir));
        }

        if ($configurationAdvPath) {
            copy($configurationAdvPath, sprintf('%s/Marlin/Configuration_adv.h', $marlinDir));
        }
    }

    private function compileAndCaptureOutput(string $marlinDir): string
    {
        $process = new Process([$this->cppPath, '-dM']);
        $process->setWorkingDirectory($marlinDir.'/Marlin/src');
        $process->setInput((string) file_get_contents($this->cppMinionPath));
        $process->run();

        $result = $process->getOutput();
        if (empty($result)) {
            throw new EmptyConfigurationException();
        }

        return $result;
    }

    private function checkPreprocessor(): void
    {
        $process = new Process([$this->cppPath, '--version']);
        try {
            if (0 === $process->run()) {
                return;
            }
        } catch (ProcessStartFailedException) {
            // ignored intentionally
        }

        throw new PreprocessorNotCallableException();
    }
}
