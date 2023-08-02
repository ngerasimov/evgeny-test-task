<?php

namespace App\Repository;

use App\Entity\MeasureType;
use App\Entity\Module;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    /**
     * @return array<Module>
     */
    public function getList()
    {
        $qb = $this->createQueryBuilder('m')
            ->orderBy('m.name')
        ;

        return $qb->getQuery()->getResult();
    }

    public function getCommonSeriesByPeriod(Module $module, MeasureType $measureType, CarbonImmutable $begin, CarbonImmutable $end)
    {
        return $this->getEntityManager()->getConnection()->executeQuery(<<<EOS
select datetime, value, 'v' as type
from measured_value v
where module_id = :module
    and type_id = :measure
    and datetime between :begin and :end
union all
select datetime, null as value, 's' as type
from state_history sh
join state s on s.id = sh.state_id
where module_id = :module
    and is_operable = 0 
    and datetime between :begin and :end
order by datetime, type
EOS,

            [
                'module' => $module->getId(),
                'measure' => $measureType->getId(),
                'begin' => $begin,
                'end' => $end,
            ]
        )->fetchAllAssociative();

    }
}
