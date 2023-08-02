<?php

namespace App\Simulation;

use App\Simulation\Model\Event;

/**
 * Just simulation events ordered by timestamp
 */
class EventQueue
{
    public function __construct()
    {
        $x = 1;
    }

    /** @var array<Event> */
    private array $events;

    public function count()
    {
        return count($this->events);
    }

    public function willBe(): ?Event
    {
        return reset($this->events) ?: null;
    }

    public function out(): ?Event
    {
        return array_shift($this->events);

    }

    public function in(Event $event)
    {
        $this->events[] = $event;
        usort(
            $this->events,
            /**
             * @param Event $e1
             * @param Event $e2
             */
            fn($e1, $e2) => $e1->getTimestamp() < $e2->getTimestamp()
                ? -1 : ($e1->getTimestamp() > $e2->getTimestamp() ? 1 : 0)
        );
    }

    public function hasLike(Event $event): bool
    {
        foreach ($this->events as $queuedEvent) {
            if (
                $event->getType() === $queuedEvent->getType()
                && ($event->getModule()?->getCode() ?? '') === ($queuedEvent->getModule()?->getCode() ?? '')
                && ($event->getMeasure()?->getCode() ?? '') === ($queuedEvent->getMeasure()?->getCode() ?? '')
            ) {
                return true;
            }
        }

        return false;
    }
}