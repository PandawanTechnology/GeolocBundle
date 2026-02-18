<?php

declare(strict_types=1);

namespace PandawanTechnology\GeolocBundle\Downloader;

use Symfony\Component\HttpClient\HttpClient;

class BANDownloader
{
    public function __construct()
    {
    }

    public function download($fileName): void
    {
        $httpClient = HttpClient::create([
            'timeout' => 60,
        ]);

        $response = $httpClient
            ->request(
                'GET',
                'https://adresse.data.gouv.fr/data/ban/adresses/latest/csv/adresses-france.csv.gz',
            )
        ;

        $fileHandler = fopen($fileName, 'w');

        foreach ($httpClient->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        fclose($fileHandler);
    }
}
