<?php

namespace App\Controller;

use App\Entity\MeasureType;
use App\Entity\Module;
use App\Model\ModuleListResponseItem;
use App\Repository\MeasureTypeRepository;
use App\Repository\ModuleRepository;
use App\Repository\StateHistoryRepository;
use App\Repository\StateRepository;
use App\Utils\DateIntervalAggregations;
use App\Utils\ValueSeriesAdaptation;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class ModuleController extends AbstractController
{
    #[Route('/', name: 'app_module_index', methods: ['GET'])]
    public function index(
        ModuleRepository $moduleRepository,
        StateHistoryRepository $stateHistoryRepository,
        MeasureTypeRepository $measureTypeRepository,
        StateRepository $stateRepository,
    ): Response
    {
        $moduleList = $moduleRepository->getList();
        $response = [];
        foreach ($moduleList as $module) {
            $currentStateHistoryRecord = $stateHistoryRepository->getLastSavedModuleState($module);
            $availableMeasures = $measureTypeRepository->getAvailableModuleMeasuresByPeriod($module);

            $stateHistory = $stateHistoryRepository->getModuleStatesByPeriod($module);

            $responseItem = new ModuleListResponseItem(
                $module,
                $currentStateHistoryRecord?->getState(),
                $availableMeasures,
            );
            $responseItem->historyLength = DateIntervalAggregations::calcSumIntervalsByStateIds($stateHistory, new CarbonImmutable());

            $response[] = $responseItem;
        }

        return $this->render('module/index.html.twig', [
            'modules' => $response,
            'states' => $stateRepository->findAllIndexed(),
        ]);
    }

    #[Route('/{id}/{measureType}/{begin?-10min}/{end?now}', name: 'app_module_show', methods: ['GET'])]
    public function show(
        Module $module,
        MeasureType $measureType,
        \DateTimeInterface $begin,
        \DateTimeInterface $end,
        ModuleRepository $moduleRepository,
        int $chartTimeLength,
        int $chartTimeStep,
    ): Response
    {
        $beginDate = new CarbonImmutable($begin);
        $endDate = new CarbonImmutable($end);

        $timeLength = $endDate->getTimestamp() - $beginDate->getTimestamp();

        if ($timeLength > $chartTimeLength) {
            $beginDate = $endDate->subSeconds($chartTimeLength);
            $timeLength = $endDate->getTimestamp() - $beginDate->getTimestamp();
        }
        $values = $moduleRepository->getCommonSeriesByPeriod($module, $measureType, $beginDate, $endDate);

        if (count($values) > 1) {
            $beginDate = new CarbonImmutable($values[0]['datetime']);
            $endDate = new CarbonImmutable($values[array_key_last($values) ?? 0]['datetime']);
            $timeLength = $endDate->getTimestamp() - $beginDate->getTimestamp();
        }
        else {
            $timeLength = 0;
        }

        return $this->render('module/show.html.twig', [
            'series' => $timeLength > 0 ? ValueSeriesAdaptation::smoothByEqualSteps($values, max(5, (int)ceil($timeLength / $chartTimeStep))) : null,
            'module' => $module,
            'measure' => $measureType,
            'begin' => $beginDate->format('H:i:s'),
            'end' => $endDate->format('H:i:s'),
            'defaultTimeLength' => $chartTimeLength,
        ]);
    }
}
