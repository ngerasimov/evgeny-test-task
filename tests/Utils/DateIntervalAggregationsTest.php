<?php

namespace Tests\Utils;

use App\Repository\StateHistoryRepository;
use App\Utils\DateIntervalAggregations;
use Carbon\CarbonImmutable;
use Tests\FixturesTestCase;

/**
 * @see DateIntervalAggregations
 */
class DateIntervalAggregationsTest extends FixturesTestCase
{
    public function testCalcSumIntervalsByStateIds()
    {
        $this->loadFixturesFromFiles(['fixtures/all_test.yaml']);

        $repository = self::getContainer()->get(StateHistoryRepository::class);

        $stateHistory = $repository->getModuleStatesByPeriod($this->fixtures['module.phone']);
        $result = DateIntervalAggregations::calcSumIntervalsByStateIds($stateHistory, new CarbonImmutable('+20 min'));

        self::assertArrayHasKey(0, $result);
        self::assertInstanceOf(\DateInterval::class, $result[0]);
        self::assertArrayHasKey($this->fixtures['state.operate']->getId(), $result);
        self::assertInstanceOf(\DateInterval::class, $result[$this->fixtures['state.operate']->getId()]);
        self::assertArrayHasKey($this->fixtures['state.breakdown']->getId(), $result);
        self::assertInstanceOf(\DateInterval::class, $result[$this->fixtures['state.breakdown']->getId()]);
    }
}