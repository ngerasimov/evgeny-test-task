<?php

namespace Tests\Repository;

use App\Entity\Module;
use App\Repository\ModuleRepository;
use Carbon\CarbonImmutable;
use Tests\FixturesTestCase;

/**
 * @see ModuleRepository
 */
class ModuleRepositoryTest extends FixturesTestCase
{
    private ModuleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixturesFromFiles(['fixtures/all_test.yaml']);
        $this->repository = static::getContainer()->get(ModuleRepository::class);
    }


    public function testGetList()
    {
        $list = $this->repository->getList();

        $moduleFixturesCount = count(array_filter($this->fixtures, fn($i) => str_starts_with($i, 'module.'), ARRAY_FILTER_USE_KEY));

        self::assertIsArray($list);
        self::assertCount($moduleFixturesCount, $list);
        self::assertInstanceOf(Module::class, $list[0] ?? null);
    }

    public function testGetCommonSeriesByPeriod()
    {
        $series = $this->repository->getCommonSeriesByPeriod(
            $this->fixtures['module.phone'],
            $this->fixtures['measure_type.signal_strength'],
            new CarbonImmutable($this->fixtures['module_phone.measure_signal100']->getDatetime()),
            new CarbonImmutable($this->fixtures['module_phone.measure_signal1']->getDatetime()),
        );

        self::assertIsArray($series);
        self::assertArrayHasKey(0, $series);
        self::assertArrayHasKey('datetime', $series[0]);
        self::assertArrayHasKey('value', $series[0]);
        self::assertArrayHasKey('type', $series[0]);
    }
}