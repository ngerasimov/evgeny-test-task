<?php

namespace App\Message;

final class SendMeasureValueMessage
{
    public function __construct(
        public string $moduleCode,
        public string $measureCode,
        public float $value,
        public int $timestamp,
    )
    {
    }
}
