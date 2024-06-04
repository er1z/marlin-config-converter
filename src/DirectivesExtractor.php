<?php

namespace Er1z\MarlinConfigConverter;

class DirectivesExtractor
{
    public const string DEFAULT_SKIPPING_IDENTIFIERS = '^((_+)|(BOARD)|(AUX)|(HAS))|\(';

    public function __construct(
        private readonly string $skippedIdentifiersRegex = self::DEFAULT_SKIPPING_IDENTIFIERS
    ) {
    }

    public function __invoke(string $parsedHeaders): DirectivesCollection
    {
        $results = new DirectivesCollection();
        foreach (new SplitStringIterator($parsedHeaders, PHP_EOL) as $line) {
            $directive = $this->extractFromLine($line);
            if (!$directive || !$this->isUseful($directive['identifier'])) {
                continue;
            }

            $value = trim($directive['value']);
            $results[trim($directive['identifier'])] = empty($value) ? 'on' : $value;
        }

        return $results;
    }

    /**
     * @return ?array{identifier: string, value: string}
     */
    private function extractFromLine(string $line): ?array
    {
        $matches = [];
        if (!preg_match('/^#define (?<identifier>[^ ]+) ?(?<value>.*)$/', $line, $matches)) {
            return null;
        }

        return [
            'identifier' => $matches['identifier'],
            'value' => $matches['value'],
        ];
    }

    private function isUseful(string $identifier): bool
    {
        return !preg_match(sprintf('#%s#si', $this->skippedIdentifiersRegex), $identifier);
    }
}
