<?php
namespace App\Command;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city',
    description: 'Display weather forecasts by country and city',
)]
class WeatherCityCommand
{
    public function __construct(
        private WeatherUtil $weatherUtil
    ) {
    }
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Country code')] string $country,
        #[Argument('City name')] string $city,
    ): int {
        $forecasts = $this->weatherUtil->getWeatherForCountryAndCity(
            $country,
            $city
        );
        $io->title('Location: ' . $city . ' (' . $country . ')');
        if (empty($forecasts)) {
            $io->warning('No weather forecasts found.');
            return Command::SUCCESS;
        }
        $rows = [];
        foreach ($forecasts as $forecast) {
            $rows[] = [
                $forecast->getDate()->format('Y-m-d'),
                $forecast->getTemperature(),
                $forecast->getPressure(),
                $forecast->getWind(),
                $forecast->getHumidity(),
            ];
        }
        $io->table(
            ['Date', 'Temperature (°C)', 'Pressure (hPa)', 'Wind', 'Humidity (%)'],
            $rows
        );
        return Command::SUCCESS;
    }
}
