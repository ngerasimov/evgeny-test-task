<?php

namespace App\Simulation\Model;


class Event
{
    public const TYPE_INIT_MEASURE = 'init_measurement';
    public const TYPE_INIT_MODULE = 'init_module';
    public const TYPE_BREAKDOWN = 'breakdown';
    public const TYPE_RESTORE = 'restore';
    public const TYPE_STATE = 'state';
    public const TYPE_STAGE = 'stage';
    public const TYPE_TICK = 'tick';


    public function __construct(
        private string $type,
        private int $timestamp,
        private ?Measure $measure = null,
        private ?Module $module = null,
    )
    {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getMeasure(): ?Measure
    {
        return $this->measure;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }
}