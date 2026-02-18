<?php

declare(strict_types=1);

namespace PandawanTechnology\GeolocBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PandawanTechnology\GeolocBundle\Entity\Street;

/**
 * @extends ServiceEntityRepository<Street>
 */
class StreetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Street::class);
    }

    /**
     * @return array<string, Street>
     */
    public function getAllFromCityInseeCode(string $inseeCode): array
    {
        return $this->createQueryBuilder('s', 's.name')
            ->innerJoin('s.city', 'city')
            ->where('city.inseeCode = :inseeCode')
            ->setParameter('inseeCode', $inseeCode)
            ->getQuery()
            ->getResult();
    }
}
