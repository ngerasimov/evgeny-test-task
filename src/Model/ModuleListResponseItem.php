<?php

namespace App\Model;

use App\Entity\MeasureType;
use App\Entity\Module;
use App\Entity\State;
use App\Entity\StateHistory;

class ModuleListResponseItem
{
    public int $id;
    public string $name;
    public string $code;
    /** @var array<\DateInterval> */
    public array $historyLength = [];

    /**
     * @param Module $module
     * @param array<StateHistory> $stateHistory
     */
    public function __construct(
        Module $module,
        public ?State $currentState,
        /** @var array<MeasureType>  */
        public array $availableMeasures,
    )
    {
        $this->id = $module->getId() ?? 0;
        $this->name = $module->getName() ?? '';
        $this->code = $module->getCode() ?? '';
    }
}