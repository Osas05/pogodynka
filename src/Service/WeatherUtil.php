<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Location;
use App\Entity\Forecast;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;

class WeatherUtil
{
    public function __construct(
        private ForecastRepository $forecastRepository,
        private LocationRepository $locationRepository
    ) {
    }

    /**
     * @return Forecast[]
     */
    public function getWeatherForLocation(Location $location): array
    {
        return $this->forecastRepository->findByLocation($location);
    }

    /**
     * @return Forecast[]
     */
    public function getWeatherForCountryAndCity(
        string $countryCode,
        string $city
    ): array {
        $location = $this->locationRepository->findOneBy([
            'country' => $countryCode,
            'name' => $city,
        ]);

        if ($location === null) {
            return [];
        }

        return $this->getWeatherForLocation($location);
    }
}
