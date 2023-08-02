<?php

namespace App\Simulation\Model;

use App\Simulation\MeasurementGenerator;

/**
 * See docs/implementation.md for details
 */
class Measure
{
    private int $stageTsBegin;
    private int $stageTsEnd;
    private float $valueAtBegin;
    private float $valueAtEnd;

    public function __construct(
        private string $code,
        private Module $module,
        private MeasurementGenerator $generator,
        private float $valueMin,
        private float $valueMax,
        private int $tickPeriod,
        private int $stageDuration,
    )
    {
        $this->stageTsBegin = $this->generator->getCurrentTimestamp();
        $this->stageTsEnd = $this->stageTsBegin + $this->stageDuration;
        $this->valueAtBegin = $this->getValueInRange();
        $this->valueAtEnd = $this->getValueInRange();
        $this->module->addMeasure($this);
    }

    public function getNextTickTs(): ?int
    {
        if (!$this->module->isOperable()) {
            return null;
        }

        return intval($this->generator->getCurrentTimestamp() + $this->tickPeriod * (1 + mt_rand(-100, 100) / 1000)); // +-10%
    }

    public function getValue(): ?float
    {
        if (!$this->module->isOperable()) {
            return null;
        }

        $ts = $this->generator->getCurrentTimestamp();
        $this->validateRange();
        return $this->valueAtBegin
            + ($this->valueAtEnd - $this->valueAtBegin)
                * ($ts - $this->stageTsBegin)
                / ($this->stageTsEnd - $this->stageTsBegin)
        ;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    private function getValueInRange(): float
    {
        $rand = mt_rand();
        $max = mt_getrandmax();
        return $this->valueMin + $rand / $max * $this->valueMax;
    }

    private function validateRange(): void
    {
        $ts = $this->generator->getCurrentTimestamp();

        // we are still within the current stage
        if ($ts < $this->stageTsEnd) {
            return;
        }

        // we are within the next stage right after the current one
        if ($ts - $this->stageTsEnd < $this->stageDuration) {
            $this->stageTsBegin = $this->stageTsEnd;
            $this->valueAtBegin = $this->valueAtEnd;
            $this->stageTsEnd += $this->stageDuration;
            $this->valueAtEnd = $this->getValueInRange();
            return;
        }

        // we've moved far away from the current stage
        $this->stageTsBegin += intval(($ts - $this->stageTsEnd) / $this->stageDuration) * $this->stageDuration;
        $this->stageTsEnd = $this->stageTsBegin + $this->stageDuration;
        $this->valueAtBegin = $this->getValueInRange();
        $this->valueAtEnd = $this->getValueInRange();
    }

    public function getModule(): Module
    {
        return $this->module;
    }
}