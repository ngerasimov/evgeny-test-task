<?php

namespace Tests\Repository;

use App\Entity\StateHistory;
use App\Repository\StateHistoryRepository;
use Carbon\CarbonImmutable;
use Tests\FixturesTestCase;

/**
 * @see StateHistoryRepository
 */
class StateHistoryRepositoryTest extends FixturesTestCase
{
    private StateHistoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixturesFromFiles(['fixtures/all_test.yaml']);
        $this->repository = static::getContainer()->get(StateHistoryRepository::class);
    }

    public function testGetLastSavedModuleState()
    {
        $stateHistoryRec = $this->repository->getLastSavedModuleState($this->fixtures['module.phone'], new CarbonImmutable('+1 hour'));
        self::assertInstanceOf(StateHistory::class, $stateHistoryRec);
    }

    public function testGetModuleStatesByPeriod()
    {
        $stateHistory = $this->repository->getModuleStatesByPeriod(
            $this->fixtures['module.phone'],
            new CarbonImmutable($this->fixtures['module_phone.state_breakdown3']->getDatetime()->subSecond()),
            new CarbonImmutable($this->fixtures['module_phone.state_breakdown1']->getDatetime())
        );

        self::assertIsArray($stateHistory);
        self::assertInstanceOf(StateHistory::class, $stateHistory[0]);
    }
}