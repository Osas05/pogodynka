<?php

namespace App\Controller;

use App\Entity\Location;
use App\Service\WeatherUtil;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherController extends AbstractController
{
    #[Route('/weather/{name}/{country}', name: 'app_weather')]
    public function city(
        #[MapEntity(mapping: ['name' => 'name', 'country' => 'country'])]
        Location $location,
        WeatherUtil $weatherUtil
    ): Response {
        $forecasts = $weatherUtil->getWeatherForLocation($location);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'forecasts' => $forecasts,
        ]);
    }
}
