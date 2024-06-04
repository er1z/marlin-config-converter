<?php

namespace Er1z\MarlinConfigConverter\Symfony;

use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class IniEncoder implements EncoderInterface
{
    public const string FORMAT = 'ini';
    public const string PAD_KEYS = 'pad_keys';

    private const string DEFAULT_SECTION = '__default';

    /**
     * @param array<string, mixed> $context
     */
    public function encode(mixed $data, string $format, array $context = []): string
    {
        if (!is_array($data)) {
            throw new NotEncodableValueException();
        }

        $normalizedData = $this->normalizeDataToSections($data);

        $result = '';
        foreach ($normalizedData as $section => $values) {
            if (self::DEFAULT_SECTION !== $section) {
                $result .= sprintf("[%s]\n", $section);
            }

            $sectionLongestKeyLength = $this->getLongestKeyLength($values);
            foreach ($values as $key => $value) {
                $result .= sprintf("%s = %s\n", ($context[self::PAD_KEYS] ?? null) ? str_pad($key, $sectionLongestKeyLength) : $key, $value);
            }
        }

        return $result;
    }

    public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getLongestKeyLength(array $data): int
    {
        $keys = array_keys($data);
        $lengths = array_map(strlen(...), $keys);

        return max($lengths);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function normalizeDataToSections(array $data): array
    {
        $normalizedData = [self::DEFAULT_SECTION => []];

        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $normalizedData[self::DEFAULT_SECTION][$key] = $value;
                continue;
            }

            $this->flattenArray($normalizedData, $value, $key);
        }

        return $normalizedData;
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $processed
     */
    private function flattenArray(array &$result, array $processed, string $prefix): void
    {
        foreach ($processed as $key => $value) {
            if (!is_array($value)) {
                $result[$prefix][$key] = $value;
                continue;
            }

            $this->flattenArray($result, $value, sprintf('%s:%s', $prefix, $key));
        }
    }
}
