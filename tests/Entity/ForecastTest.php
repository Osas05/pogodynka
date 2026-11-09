<?php

namespace App\Tests\Entity;

use App\Entity\Forecast;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ForecastTest extends TestCase
{
    public static function dataGetFahrenheit(): array
    {
        return [
            ['0', '32'],
            ['-100', '-148'],
            ['100', '212'],
            ['-25', '-13'],
            ['-10', '14'],
            ['5', '41'],
            ['12', '53.6'],-
            ['20', '68'],
            ['36', '96.8'],
            ['75', '167'],
        ];
    }

    #[DataProvider('dataGetFahrenheit')]
    public function testGetFahrenheit(
        string $celsius,
        string $expectedFahrenheit
    ): void {
        $forecast = new Forecast();

        $forecast->setTemperature((int) $celsius);

        $this->assertEquals(
            (float) $expectedFahrenheit,
            $forecast->getFahrenheit()
        );
    }
}
