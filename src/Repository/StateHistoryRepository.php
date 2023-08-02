<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\StateHistory;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StateHistory>
 *
 * @method StateHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method StateHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method StateHistory[]    findAll()
 * @method StateHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StateHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StateHistory::class);
    }

    public function getLastSavedModuleState(Module $module, ?CarbonImmutable $before = null): ?StateHistory
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.module=:module')
            ->setParameter('module', $module)
            ->orderBy('s.datetime', 'DESC')
            ->setMaxResults(1);
        if ($before) {
            $qb->andWhere('s.datetime <= :datetime')
                ->setParameter('datetime', $before);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Module $module
     * @param CarbonImmutable|null $begin
     * @param CarbonImmutable|null $end
     * @return array<StateHistory>
     */
    public function getModuleStatesByPeriod(Module $module, ?CarbonImmutable $begin = null, ?CarbonImmutable $end = null): array
    {
        $stateBefore = null;
        if ($begin) {
            $stateBefore = $this->getLastSavedModuleState($module, $begin);
        }

        $qb = $this->createQueryBuilder('s')
            ->where('s.module=:module')
            ->setParameter('module', $module)
            ->orderBy('s.datetime', 'ASC')
        ;

        if ($begin) {
            $qb
                ->andWhere('s.datetime >= :begin')
                ->setParameter('begin', $stateBefore ? $stateBefore->getDatetime() : $begin);
        }

        if ($end) {
            $qb->andWhere('s.datetime <= :end')->setParameter('end', $end);
        }

        return $qb->getQuery()->getResult();
    }
}
