<?php
namespace App\Controller;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] string $country,
        #[MapQueryParameter] string $city,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false,
        WeatherUtil $weatherUtil
    ): Response {
        $forecasts = $weatherUtil->getWeatherForCountryAndCity($country, $city);
        if (!in_array($format, ['json', 'csv'], true)) {
            return $this->json(['error' => 'Invalid format'], 400);
        }
        if ($twig) {
            $template = $format === 'csv'
                ? 'weather_api/index.csv.twig'
                : 'weather_api/index.json.twig';

            $content = $this->renderView($template, [
                'city' => $city,
                'country' => $country,
                'measurements' => $forecasts,
            ]);

            return new Response(
                $content,
                200,
                ['Content-Type' => $format === 'csv'
                    ? 'text/plain; charset=UTF-8'
                    : 'application/json; charset=UTF-8']
            );
        }

        if ($format === 'csv') {
            $lines = ['city,country,date,celsius,fahrenheit'];
            foreach ($forecasts as $forecast) {
                $lines[] = implode(',', [
                    $city,
                    $country,
                    $forecast->getDate()->format('Y-m-d'),
                    $forecast->getTemperature(),
                    $forecast->getFahrenheit(),
                ]);
            }
            return new Response(
                implode("\n", $lines) . "\n",
                200,
                ['Content-Type' => 'text/plain; charset=UTF-8']
            );
        }
        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => array_map(fn($forecast) => [
                'date' => $forecast->getDate()->format('Y-m-d'),
                'celsius' => $forecast->getTemperature(),
                'fahrenheit' => $forecast->getFahrenheit(),
                'pressure' => $forecast->getPressure(),
                'wind' => $forecast->getWind(),
                'humidity' => $forecast->getHumidity(),
            ], $forecasts),
        ]);
    }
}
