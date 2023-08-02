<?php

namespace App\Utils;

use App\Entity\State;
use App\Entity\StateHistory;
use Carbon\CarbonImmutable;
use Tests\Utils\DateIntervalAggregationsTest;

/**
 * @see DateIntervalAggregationsTest
 */
class DateIntervalAggregations
{
    /**
     * On/Off states stats calculation
     *
     * @param array<StateHistory> $stateHistory
     * @return array
     */
    public static function calcSumIntervalsByStateIds(array $stateHistory, ?CarbonImmutable $current = null): array
    {
        $intervalByStateIds = [];
        $lastChange = $historyBegin = null;
        $state = null;
        foreach ($stateHistory as $item) {
            $itemDate = new CarbonImmutable($item->getDatetime());
            if (!isset($historyBegin)) {
                $lastChange = $historyBegin = $itemDate;
                $state = $item->getState();
                continue;
            }

            if (($state?->getCode() ?? '') === ($item->getState()?->getCode() ?? '')) {
                continue;
            }

            /** @psalm-suppress PossiblyNullArgument */
            self::iteration($intervalByStateIds, $lastChange, $state, $itemDate);

            $state = $item->getState();
            $lastChange = $itemDate;
        }

        if ($lastChange && $current && $current > $lastChange) {
            /** @psalm-suppress PossiblyNullArgument */
            self::iteration($intervalByStateIds, $lastChange, $state, $current);
        }

        return $intervalByStateIds;
    }

    private static function iteration(array &$intervalByStateIds, CarbonImmutable $lastChange, State $state, CarbonImmutable $itemDate)
    {
        if (isset($intervalByStateIds[0])) {
            $subIntervalDateTotal = $lastChange->sub($intervalByStateIds[0]);
        }
        else {
            $subIntervalDateTotal = $lastChange;
        }

        if (isset($intervalByStateIds[$state->getId() ?? 0])) {
            $subIntervalDate = $lastChange->sub($intervalByStateIds[$state->getId() ?? 0]);
        }
        else {
            $subIntervalDate = $lastChange;
        }

        $intervalByStateIds[0] = $subIntervalDateTotal->diff($itemDate);
        $intervalByStateIds[$state->getId() ?? 0] = $subIntervalDate->diff($itemDate);
    }
}