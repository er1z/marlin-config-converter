<?php

namespace Er1z\MarlinConfigConverter;

use Er1z\MarlinConfigConverter\Exception\VersionInformationNotFoundException;

class VersionResolver
{
    private const string VERSION_INFO_LINE = '#define CONFIGURATION_H_VERSION';
    private const int STARTING_DIVIDER = 1000000;

    public function __invoke(string $configurationPath): string
    {
        $file = new \SplFileObject($configurationPath);

        foreach ($file as $line) {
            assert(is_string($line));

            if (!str_starts_with($line, self::VERSION_INFO_LINE)) {
                continue;
            }

            $version = explode(self::VERSION_INFO_LINE, $line)[1] ?? null;
            if (!$version) {
                break;
            }

            return $this->extractShortVersion(intval($version));
        }

        throw new VersionInformationNotFoundException();
    }

    private function extractShortVersion(int $version): string
    {
        $result = [];
        $divider = self::STARTING_DIVIDER;

        do {
            $component = intdiv($version, (int) $divider);
            $result[] = $component;
            $version -= $component * $divider;
            $divider /= 100;
        } while ($divider >= 1);

        return implode('.', $result);
    }
}
