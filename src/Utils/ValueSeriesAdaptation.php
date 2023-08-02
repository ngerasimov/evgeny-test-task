<?php

namespace App\Utils;


/**
 *
 */
class ValueSeriesAdaptation
{
    public static function smoothByEqualSteps(array $series, int $stepsCount): array
    {
        if (count($series) < 2) {
            return [];
        }
        $firstItem = reset($series);
        $lastItem = $series[array_key_last($series)];
        $tsBegin = strtotime($firstItem['datetime']);
        $tsEnd = strtotime($lastItem['datetime']);
        $intervalLength = $tsEnd - $tsBegin;
        if (!$intervalLength) {
            return [];
        }

        $timeStep = $intervalLength / $stepsCount;

        $grouped = [];
        $idx = 0;
        $lastValue = 0;
        $max = null;
        foreach ($series as $item) {
            $idxNew = intval((strtotime($item['datetime']) - $tsBegin) / $timeStep);
            if ($idxNew > $idx) {
                $idx++;
            }
            while ($idx < $idxNew) {
                if (isset($lastValue)) {
                    $grouped[$idx][] = $lastValue;
                } else {
                    $grouped[$idx] = null;
                }
                $idx++;
            }

            if (isset($item['value'])) {
                $grouped[$idx][] = $item['value'];
            } else {
                $grouped[$idx] = null;
            }

            $lastValue = $item['value'];
            if (!isset($max)) {
                $max = $lastValue;
            }

            $max = max($max, $lastValue);
        }

        $result = [];
        foreach ($grouped as $idx => $avg) {
            $label = date('H:i:s', intval($tsBegin + $timeStep * $idx));
            $result['labels'][] = $label;
            if (is_array($avg)) {
                $result['values'][] = array_sum($avg) / count($avg);
                $result['operate'][] = 'null';
            } else {
                $result['values'][] = 'null';
                $result['operate'][] = $max;
            }
        }

        return $result;
    }
}