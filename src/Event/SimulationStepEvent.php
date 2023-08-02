<?php

namespace App\Event;

use App\Simulation\Model\Event;

final class SimulationStepEvent
{
    public function __construct(
        private Event $event
    )
    {
    }

    public function getEvent(): Event
    {
        return $this->event;
    }
}
