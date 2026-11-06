<?php

namespace App\Command;
use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Display weather forecasts for a location',
)]
class WeatherLocationCommand
{
    public function __construct(
        private LocationRepository $locationRepository,
        private WeatherUtil $weatherUtil
    ) {
    }
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Location ID')] int $id,
    ): int {
        $location = $this->locationRepository->find($id);
        if (!$location) {
            $io->error('Location not found.');
            return Command::FAILURE;
        }
        $forecasts = $this->weatherUtil->getWeatherForLocation($location);
        $io->title('Location: ' . $location->getName());
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
