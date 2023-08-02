<?php

namespace App\Simulation;

use App\Message\SendMeasureValueMessage;
use App\Message\SendModuleStateMessage;
use App\Simulation\Model\Event;
use App\Simulation\Model\Measure;
use App\Simulation\Model\Module;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MeasurementGenerator
{
    private int $currentTimestamp;

    private int $timeLimit;

    public function __construct(
        private MessageBusInterface $bus,
        private EventDispatcherInterface $eventDispatcher,
        private EventQueue $eventQueue,
        private array $measureParams,
        private array $moduleParams,
        private bool $isRealtime,
    )
    {
        $this->currentTimestamp = time();
    }

    public function addModule(\App\Entity\Module $moduleEntity)
    {
        $moduleCode = $moduleEntity->getCode() ?? '';

        $measurements = array_map(fn($e) => $e->getCode(), $moduleEntity->getMeasureTypes()->toArray());

        if (!$measurements) {
            return;
        }

        $module = new Module($moduleCode, $this, ...$this->getModuleParams($moduleCode));

        $this->scheduleInitModule($module);

        foreach ($measurements as $measureCode) {

            $measure = new Measure($measureCode ?? '', $module, $this, ...$this->getMeasureParams($measureCode));

            $this->scheduleInitMeasure($measure);

        }
    }

    public function start(int $simulationLength): void
    {
        $this->timeLimit = $this->currentTimestamp + $simulationLength;
        $event = $this->eventQueue->out();
        if ($event instanceof Event) {
            $this->eventDispatcher->dispatch($event);
        }
    }

    #[AsEventListener]
    public function step(Event $event): void
    {
        $this->processEvent($event);

        $event = $this->eventQueue->out();

        if ($event instanceof Event) {

            $ts = $event->getTimestamp();

            if ($ts > $this->timeLimit) {
                return;
            }

            if ($this->isRealtime && $ts > time()) {
                time_sleep_until($ts);
            }

            $this->currentTimestamp = $ts;

            $this->eventDispatcher->dispatch($event);
        }
    }

    public function getCurrentTimestamp(): int
    {
        return $this->currentTimestamp;
    }

    private function processEvent(Event $event)
    {
        $measure = $event->getMeasure();
        if ($measure instanceof Measure) {
            switch ($event->getType()) {

                case Event::TYPE_INIT_MEASURE:
                    $this->scheduleTick($measure);

                    break;

                case Event::TYPE_TICK:
                    $this->scheduleTick($measure);
                    $value = $measure->getValue();
                    if (isset($value)) {
                        $this->bus->dispatch(
                            new SendMeasureValueMessage($measure->getModule()->getCode(), $measure->getCode(), $value, $event->getTimestamp())
                        );
                    }

                    break;
            }
        }

        $module = $event->getModule();
        if ($module instanceof Module) {
            switch ($event->getType()) {

                case Event::TYPE_INIT_MODULE:
                    $this->scheduleBreakdown($module);
                    $this->bus->dispatch(new SendModuleStateMessage($module->getCode(), 'operate', $this->currentTimestamp));

                    break;

                case Event::TYPE_RESTORE:
                    $module->setIsOperable(true);
                    $this->scheduleBreakdown($module);
                    $moduleCode = $module->getCode();
                    $this->bus->dispatch(new SendModuleStateMessage($moduleCode, 'operate', $this->currentTimestamp));
                    foreach ($module->getMeasures() as $measureCode => $measure) {
                        $value = $measure->getValue();
                        if (isset($value)) {
                            $this->bus->dispatch(new SendMeasureValueMessage($moduleCode, $measureCode, $value, $this->currentTimestamp));
                        }
                        $this->scheduleTick($measure);
                    }
                    break;

                case Event::TYPE_BREAKDOWN:
                    $module->setIsOperable(false);
                    $this->scheduleRestore($module);
                    $this->bus->dispatch(new SendModuleStateMessage($module->getCode(), 'breakdown', $this->currentTimestamp));

                    break;
            }
        }
    }

    private function getMeasureParams($measureCode): array
    {
        if (isset($this->measureParams[$measureCode])) {
            return $this->measureParams[$measureCode];
        } else {
            if (!isset($this->measureParams['default'])) {
                throw new \InvalidArgumentException('Default measure simulation parameters must be specified');
            }
            return $this->measureParams['default'];
        }
    }

    private function getModuleParams($moduleCode): array
    {
        if (isset($this->moduleParams[$moduleCode])) {
            return $this->moduleParams[$moduleCode];
        } else {
            if (!isset($this->moduleParams['default'])) {
                throw new \InvalidArgumentException('Default module simulation parameters must be specified');
            }
            return $this->moduleParams['default'];
        }
    }

    private function scheduleInitModule(Module $module): void
    {
        $this->eventQueue->in(new Event(Event::TYPE_INIT_MODULE, $this->currentTimestamp, null, $module));
    }

    private function scheduleInitMeasure(Measure $measure): void
    {
        $this->eventQueue->in(new Event(Event::TYPE_INIT_MEASURE, $this->currentTimestamp, $measure));
    }

    private function scheduleTick(Measure $measure): void
    {
        $nextTick = $measure->getNextTickTs();
        if ($nextTick) {
            $event = new Event(Event::TYPE_TICK, $nextTick, $measure);
            if (!$this->eventQueue->hasLike($event))
                $this->eventQueue->in($event);
        }
    }

    private function scheduleBreakdown(Module $module): void
    {
        $nextBreakdown = $module->getNextBreakdownTs();
        if ($nextBreakdown) {
            $this->eventQueue->in(new Event(Event::TYPE_BREAKDOWN, $nextBreakdown, null, $module));
        }
    }

    private function scheduleRestore(Module $module): void
    {
        $nextRestore = $module->getNextRestoreTs();
        if ($nextRestore) {
            $this->eventQueue->in(new Event(Event::TYPE_RESTORE, $nextRestore, null, $module));
        }
    }

    public function setIsRealtime(bool $isRealtime): void
    {
        $this->isRealtime = $isRealtime;
    }

    public function setCurrentTimestamp(int $currentTimestamp): void
    {
        $this->currentTimestamp = $currentTimestamp;
    }
}