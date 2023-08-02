<?php

namespace Tests\Repository;

use App\Repository\MeasureTypeRepository;
use Carbon\CarbonImmutable;
use Tests\FixturesTestCase;

/**
 * @see MeasureTypeRepository
 */
class MeasureTypeRepositoryTest extends FixturesTestCase
{
    private MeasureTypeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixturesFromFiles(['fixtures/all_test.yaml']);
        $this->repository = static::getContainer()->get(MeasureTypeRepository::class);
    }

    public function testGetAvailableModuleMeasuresByPeriod()
    {
        $availableMeasures = $this->repository->getAvailableModuleMeasuresByPeriod(
            $this->fixtures['module.phone'],
            new CarbonImmutable($this->fixtures['module_phone.measure_signal23']->getDatetime()),
            new CarbonImmutable($this->fixtures['module_phone.measure_signal1']->getDatetime())
        );

        self::assertIsArray($availableMeasures);
        self::assertCount(2, $availableMeasures);
    }
}