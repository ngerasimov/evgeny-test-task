<?php

namespace Tests\Utils;

use App\Utils\ValueSeriesAdaptation;
use Carbon\CarbonImmutable;
use Tests\FixturesTestCase;

/**
 * @see ValueSeriesAdaptation
 */
class ValueSeriesAdaptationTest extends FixturesTestCase
{
    public function testSmoothByEqualSteps()
    {
        $series = [
            ['datetime' => new CarbonImmutable('-100 sec'), 'value' => 5],
            ['datetime' => new CarbonImmutable('-85 sec'), 'value' => 15],
            ['datetime' => new CarbonImmutable('-69 sec'), 'value' => 25],
            ['datetime' => new CarbonImmutable('-68 sec'), 'value' => 20],
            ['datetime' => new CarbonImmutable('-67 sec'), 'value' => 21],
            ['datetime' => new CarbonImmutable('-66 sec'), 'value' => null],
            ['datetime' => new CarbonImmutable('-45 sec'), 'value' => 31],
            ['datetime' => new CarbonImmutable('-35 sec'), 'value' => null],
            ['datetime' => new CarbonImmutable(), 'value' => 51],
        ];

        $result = ValueSeriesAdaptation::smoothByEqualSteps($series, 10);

        self::assertIsArray($result);
        self::assertArrayHasKey('labels', $result);
        self::assertArrayHasKey('values', $result);
        self::assertArrayHasKey('operate', $result);
        self::assertCount(11, $result['labels']);
    }
}