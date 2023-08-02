<?php

namespace App\Repository;

use App\Entity\MeasuredValue;
use App\Entity\MeasureType;
use App\Entity\Module;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MeasuredValue>
 *
 * @method MeasuredValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeasuredValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeasuredValue[]    findAll()
 * @method MeasuredValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeasuredValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeasuredValue::class);
    }

    /**
     * @param Module $module
     * @param MeasureType $measure
     * @param CarbonImmutable $begin
     * @param CarbonImmutable $end
     * @return array<MeasuredValue>
     */
    public function getMeasuredValuesByPeriod(Module $module, MeasureType $measure, CarbonImmutable $begin, CarbonImmutable $end): array
    {
        $qb = $this->createQueryBuilder('v')
            ->where('v.type=:measure')
            ->andWhere('v.module=:module')
            ->setParameter('measure', $measure)
            ->setParameter('module', $module)
            ->andWhere('v.datetime BETWEEN :begin AND :end')
            ->setParameter('begin', $begin)
            ->setParameter('end', $end)
            ->orderBy('v.datetime')
        ;

        return $qb->getQuery()->getResult();
    }
}
