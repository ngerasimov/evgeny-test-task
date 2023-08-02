<?php

namespace App\Message;

final class SendModuleStateMessage
{
    public function __construct(
        public string $moduleCode,
        public string $state,
        public int $timestamp
    )
    {
    }
}
