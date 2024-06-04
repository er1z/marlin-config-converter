<?php

namespace Tests\Er1z\MarlinConfigConverter\Unit;

use Er1z\MarlinConfigConverter\Exception\VersionInformationNotFoundException;
use Er1z\MarlinConfigConverter\VersionResolver;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VersionResolverTest extends TestCase
{
    private vfsStreamDirectory $fs;
    private VersionResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = vfsStream::setup();
        $this->resolver = new VersionResolver();
    }

    /**
     * @return array<string[]>
     */
    public static function provideVersionStringToVersion(): array
    {
        return [
            ['02010202', '2.1.2.2'],
            ['02000905', '2.0.9.5'],
        ];
    }

    #[DataProvider('provideVersionStringToVersion')]
    public function testVersionSolving(string $value, string $expected): void
    {
        $file = new vfsStreamFile('Configuration.h');
        $this->fs->addChild($file);
        file_put_contents($file->url(), <<<FILE
// some comment
return 0;
#define CONFIGURATION_H_VERSION $value
FILE);
        self::assertEquals($expected, ($this->resolver)($file->url()));
    }

    public function testVersionNotFound(): void
    {
        $file = new vfsStreamFile('Configuration.h');
        $this->fs->addChild($file);

        self::expectException(VersionInformationNotFoundException::class);
        ($this->resolver)($file->url());
    }

    public function testVersionNotFoundForBrokenValue(): void
    {
        $file = new vfsStreamFile('Configuration.h');
        $this->fs->addChild($file);
        file_put_contents($file->url(), '#define CONFIGURATION_H_VERSION');

        self::expectException(VersionInformationNotFoundException::class);
        ($this->resolver)($file->url());
    }
}
