<?php

namespace Er1z\MarlinConfigConverter\Action;

use Er1z\MarlinConfigConverter\ConfigurationExtractor;
use Er1z\MarlinConfigConverter\DirectivesCollection;
use Er1z\MarlinConfigConverter\DirectivesExtractor;
use Er1z\MarlinConfigConverter\MarlinDownloader;
use Er1z\MarlinConfigConverter\VersionResolver;

class GenerateConfigAction
{
    public function __construct(
        private readonly ConfigurationExtractor $configurationExtractor,
        private readonly DirectivesExtractor $directivesExtractor,
        private readonly VersionResolver $versionResolver,
        private readonly MarlinDownloader $marlinDownloader
    ) {
    }

    public function __invoke(
        string $configurationPath,
        ?string $configurationAdvPath = null
    ): DirectivesCollection {
        $version = ($this->versionResolver)($configurationPath);
        $marlinPath = ($this->marlinDownloader)($version);

        $baseConfiguration = ($this->configurationExtractor)($marlinPath);
        $baseDirectives = ($this->directivesExtractor)($baseConfiguration);

        $customizedConfiguration = ($this->configurationExtractor)($marlinPath, $configurationPath, $configurationAdvPath);
        $customizedDirectives = ($this->directivesExtractor)($customizedConfiguration);

        return $customizedDirectives->diff($baseDirectives);
    }
}
