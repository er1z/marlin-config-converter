<?php

namespace Tests\Er1z\MarlinConfigConverter\Integration;

use Er1z\MarlinConfigConverter\Exception\MarlinDownloadFailedException;
use Er1z\MarlinConfigConverter\MarlinDownloader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class MarlinDownloaderTest extends TestCase
{
    private \org\bovigo\vfs\vfsStreamDirectory $vfs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vfs = vfsStream::setup();
    }

    private function loadZipStub(): void
    {
        $tagsDir = vfsStream::newDirectory('archive/refs/tags');
        $this->vfs->addChild($tagsDir->at($this->vfs));
        /** @var vfsStreamDirectory $tagsDir */
        $tagsDir = $this->vfs->getChild('archive/refs/tags');
        $tagsDir->addChild(
            (new vfsStreamFile('1.0.0.zip'))->setContent((string) file_get_contents(sprintf('%s/../stubs/Marlin-1.0.0.zip', __DIR__)))
        );
    }

    public function testDownload(): void
    {
        $this->loadZipStub();

        $downloader = new MarlinDownloader(
            urlBaseline: $this->vfs->url()
        );
        $path = $downloader('1.0.0');

        self::assertFileExists(sprintf('%s/stub', $path));
        $downloader = null;
        self::assertFileDoesNotExist(sprintf('%s/stub', $path));
    }

    public function testDownloadFailure(): void
    {
        self::expectException(MarlinDownloadFailedException::class);

        $downloader = new MarlinDownloader(
            urlBaseline: $this->vfs->url()
        );
        $downloader('1.0.0');
    }
}
