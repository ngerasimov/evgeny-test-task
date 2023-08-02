<?php

namespace App\Repository;

use App\Entity\MeasureType;
use App\Entity\Module;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MeasureType>
 *
 * @method MeasureType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeasureType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeasureType[]    findAll()
 * @method MeasureType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeasureTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeasureType::class);
    }

    public function getAvailableModuleMeasuresByPeriod(Module $module, ?CarbonImmutable $begin = null, ?CarbonImmutable $end = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.id, m.name, COUNT(v.id) AS count, MIN(v.datetime) AS first, MAX(v.datetime) AS last')
            ->join('m.measuredValues', 'v')
            ->andWhere('v.module=:module')
            ->setParameter('module', $module)
            ->groupBy('m.id')
        ;

        if ($begin) {
            $qb->andWhere('v.datetime >= :begin')->setParameter('begin', $begin);
        }

        if ($end) {
            $qb->andWhere('v.datetime <= :end')->setParameter('end', $end);
        }

        $result = $qb->getQuery()->getResult();
        foreach ($result as &$refItem) {
            $refItem['first'] = new CarbonImmutable($refItem['first']);
            $refItem['last'] = new CarbonImmutable($refItem['last']);
        }

        return $result;
    }
}
