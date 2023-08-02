<?php

namespace App\MessageHandler;

use App\Entity\MeasuredValue;
use App\Message\SendMeasureValueMessage;
use App\Repository\MeasureTypeRepository;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendMeasureValueMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private ModuleRepository $moduleRepository,
        private MeasureTypeRepository $measureTypeRepository,
    )
    {
    }

    public function __invoke(SendMeasureValueMessage $message) {
        $this->persist($message);
        $this->log($message);
    }

    private function persist(SendMeasureValueMessage $message) {
        $measuredValue = new MeasuredValue();

        /** @psalm-suppress UndefinedMagicMethod */
        $module = $this->moduleRepository->findOneByCode($message->moduleCode);
        $measuredValue->setModule($module);

        /** @psalm-suppress UndefinedMagicMethod */
        $type = $this->measureTypeRepository->findOneByCode($message->measureCode);
        $measuredValue->setType($type);

        $measuredValue->setValue($message->value);

        $measuredValue->setDatetime((new \DateTime())->setTimestamp($message->timestamp));

        $this->entityManager->persist($measuredValue);
        $this->entityManager->flush();
    }

    private function log(SendMeasureValueMessage $message)
    {
        $this->logger->notice(
            "measure",
            [
                'module' => $message->moduleCode,
                'measure' => $message->measureCode,
                'timestamp' => $message->timestamp,
                'value' => $message->value,
            ]
        );
    }
}
