<?php
namespace App\Repository;
use App\Entity\Forecast;
use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ForecastRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forecast::class);
    }

    public function findByLocation(Location $location)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.location = :location')
            ->setParameter('location', $location)
            ->andWhere('f.date > :now')
            ->setParameter('now', date('Y-m-d'));

        $query = $qb->getQuery();
        $result = $query->getResult();

        return $result;
    }
}
