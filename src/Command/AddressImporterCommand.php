<?php

declare(strict_types=1);

namespace PandawanTechnology\GeolocBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use PandawanTechnology\GeolocBundle\Entity\Address;
use PandawanTechnology\GeolocBundle\Entity\City;
use PandawanTechnology\GeolocBundle\Entity\Street;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'pandawan-geoloc:data:import',
    description: 'Import data from la Base Adresse Nationale\' service',
)]
class AddressImporterCommand extends Command
{
    public function __construct(
        private readonly string $shareDir,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileName = $this->shareDir.'/'.DownloaderCommand::FILE_NAME;

        $csvReader = Reader::from($fileName)
                     ->setHeaderOffset(0)
                     ->setDelimiter(';')
        ;

        $citiesMap = [];
        $repetitors = []; // Prevents duplications in the case of multiple addresses with the same number and repetitor within the same street

        foreach ($csvReader->getRecords() as $i => $record) {
            try {
                $currentInseeCode = $record['code_insee'];

                if (!isset($citiesMap[$currentInseeCode])) {
                    $output->writeln(sprintf('<info>Found a new INSEE code: %s</info>', $currentInseeCode));
                    $this->entityManager->flush();

                    $currentCity = new City()
                        ->setCountryCode('FR')
                        ->setName($record['nom_commune'])
                        ->setInseeCode($currentInseeCode)
                        ->setPostCode($record['code_postal']);

                    $this->entityManager->persist($currentCity);
                    $this->entityManager->flush();

                    $citiesMap[$currentInseeCode] = $currentCity;
                } else {
                    $currentCity = $citiesMap[$currentInseeCode];
                }

                $currentStreetName = $record['nom_voie'];

                if (!$currentStreet = $this->findStreetWithinCity($currentCity, $currentStreetName)) {
                    $output->writeln(sprintf('<info>Found a new street name: %s</info>', $currentStreetName));

                    $currentStreet = new Street()
                        ->setName($currentStreetName);
                    $currentCity->addStreet($currentStreet);

                    $this->entityManager->persist($currentStreet);
                }

                $addressNumber = $record['numero'];
                $addressRepetitor = $record['rep'] ?: null;

                if (isset($repetitors[$currentInseeCode][$currentStreetName][$addressNumber][$addressRepetitor])) {
                    continue;
                }

                $this->entityManager->persist(
                    new Address()
                        ->setStreet($currentStreet)
                        ->setNumber($addressNumber)
                        ->setRepetitor($addressRepetitor)
                        ->setLatitude($record['lat'])
                        ->setLongitude($record['lon'])
                );

                $repetitors[$currentInseeCode][$currentStreetName][$addressNumber][$addressRepetitor] = true;
            } catch (\Throwable $throwable) {
                dump('Line #'.$i);
                dump($record);

                throw $throwable;
            }
        }

        $this->entityManager->flush();

        return static::SUCCESS;
    }

    private function findStreetWithinCity(City $currentCity, string $currentStreetName): ?Street
    {
        return $currentCity->getStreets()->findFirst(
            static fn (int $key, Street $street) => $street->getName() === $currentStreetName
        );
    }
}
