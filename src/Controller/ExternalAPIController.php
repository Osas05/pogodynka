<?php

namespace App\Controller;

use App\Form\ExternalAPIType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Attribute\Route;

final class ExternalAPIController extends AbstractController
{
    #[Route('/external/api', name: 'app_external_api')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ExternalAPIType::class);
        $form->handleRequest($request);

        $weatherData = null;
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $latitude = $data['latitude'];
            $longitude = $data['longitude'];

            $apiUrl = sprintf(
                'https://api.open-meteo.com/v1/forecast?latitude=%s&longitude=%s&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&timezone=UTC',
                $latitude,
                $longitude
            );

            try {
                $client = HttpClient::create();
                $response = $client->request('GET', $apiUrl);
                $weatherData = $response->toArray();
            } catch (\Exception $e) {
                $error = 'Error fetching weather data.';
            }
        }

        return $this->render('external_api/index.html.twig', [
            'form' => $form->createView(),
            'weatherData' => $weatherData,
            'error' => $error,
        ]);
    }
}
