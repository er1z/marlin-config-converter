<?php

namespace Er1z\MarlinConfigConverter;

use Er1z\MarlinConfigConverter\Exception\MarlinDownloadFailedException;
use Symfony\Component\Filesystem\Filesystem;

class MarlinDownloader
{
    /**
     * @var string[]
     */
    protected array $toClear = [];

    private readonly string $tmpDirectory;

    public function __construct(
        ?string $tmpDirectory = null,
        private readonly string $urlBaseline = 'https://github.com/MarlinFirmware/Marlin'
    ) {
        $this->tmpDirectory = $tmpDirectory ?? sys_get_temp_dir();
    }

    public function __invoke(string $version): string
    {
        $path = sprintf('%s/Marlin-%s.zip', $this->tmpDirectory, $version);
        $downloadedFile = @file_get_contents(sprintf('%s/archive/refs/tags/%s.zip', $this->urlBaseline, $version));
        file_put_contents(
            $path,
            $downloadedFile
        );

        if (!$downloadedFile) {
            throw new MarlinDownloadFailedException($version);
        }

        $this->extract($path);

        return substr($path, 0, -4);
    }

    private function extract(string $path): void
    {
        $zip = new \ZipArchive();
        $zip->open($path);
        $zip->extractTo($this->tmpDirectory);
        $zip->close();

        $this->toClear[] = $path;
    }

    public function __destruct()
    {
        $filesystem = new Filesystem();
        foreach ($this->toClear as $path) {
            $filesystem->remove([$path, substr($path, 0, -4)]);
        }
    }
}
