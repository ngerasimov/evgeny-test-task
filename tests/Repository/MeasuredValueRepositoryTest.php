<?php

namespace Tests\Repository;

use App\Entity\MeasuredValue;
use App\Repository\MeasuredValueRepository;
use Carbon\CarbonImmutable;
use Tests\FixturesTestCase;

/**
 * @see MeasuredValueRepository
 */
class MeasuredValueRepositoryTest extends FixturesTestCase
{
    private MeasuredValueRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixturesFromFiles(['fixtures/all_test.yaml']);
        $this->repository = static::getContainer()->get(MeasuredValueRepository::class);
    }

    public function testGetMeasuredValuesByPeriod()
    {
        $measuredValues = $this->repository->getMeasuredValuesByPeriod(
            $this->fixtures['module.phone'],
            $this->fixtures['measure_type.signal_strength'],
            new CarbonImmutable($this->fixtures['module_phone.measure_signal23']->getDatetime()),
            new CarbonImmutable($this->fixtures['module_phone.measure_signal1']->getDatetime())
        );

        self::assertIsArray($measuredValues);
        self::assertCount(23, $measuredValues);
        self::assertInstanceOf(MeasuredValue::class, $measuredValues[0]);
    }
}