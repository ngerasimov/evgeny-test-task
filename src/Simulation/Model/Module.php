<?php

namespace App\Simulation\Model;

use App\Simulation\MeasurementGenerator;

class Module
{
    /** @var array<string, Measure> */
    private array $measures = [];

    public function __construct(
        private string $code,
        private MeasurementGenerator $generator,
        private int $breakdownPeriod,
        private int $breakdownDuration,
        private bool $isOperable = true,
    )
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }


    public function isOperable(): bool
    {
        return $this->isOperable;
    }

    public function setIsOperable(bool $isOperable): void
    {
        $this->isOperable = $isOperable;
    }

    public function getNextBreakdownTs(): ?int
    {
        if (!$this->isOperable()) {
            return null;
        }

        return intval($this->generator->getCurrentTimestamp() + $this->breakdownPeriod * (1 + mt_rand(-100, 100) / 1000)); // +-10%
    }

    public function getNextRestoreTs(): ?int
    {
        if ($this->isOperable()) {
            return null;
        }

        return intval($this->generator->getCurrentTimestamp() + $this->breakdownDuration * (1 + mt_rand(-100, 100) / 1000)); // +-10%
    }

    public function addMeasure(Measure $measure): void
    {
        $this->measures[$measure->getCode()] = $measure;
    }

    public function getMeasures(): array
    {
        return $this->measures;
    }
}