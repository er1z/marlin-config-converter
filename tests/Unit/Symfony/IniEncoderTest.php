<?php

namespace Tests\Er1z\MarlinConfigConverter\Unit\Symfony;

use Er1z\MarlinConfigConverter\Symfony\IniEncoder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class IniEncoderTest extends TestCase
{
    public function testEncoding(): void
    {
        $encoder = new IniEncoder();

        $data = [
            'into no section' => 'asdasdasss',
            'shorter' => 'bleh',
            'xyz' => [
                'awd' => [
                    'dsa' => 123,
                    'sss' => 321,
                ],
            ],
            'zzz' => [
                'asd' => 333,
            ],
        ];

        $result = $encoder->encode($data, IniEncoder::FORMAT, [IniEncoder::PAD_KEYS => 1]);

        self::assertStringEqualsStringIgnoringLineEndings(<<<INI
into no section = asdasdasss
shorter         = bleh
[xyz:awd]
dsa = 123
sss = 321
[zzz]
asd = 333

INI
            , $result);
    }

    public function testForNotEncodableValue(): void
    {
        $encoder = new IniEncoder();

        self::expectException(NotEncodableValueException::class);
        $encoder->encode('Szczebrzeszyn', $encoder::FORMAT);
    }
}
