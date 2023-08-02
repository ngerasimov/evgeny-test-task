<?php

namespace App\MessageHandler;

use App\Entity\StateHistory;
use App\Message\SendModuleStateMessage;
use App\Repository\ModuleRepository;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendModuleStateMessageHandler
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private ModuleRepository $moduleRepository,
        private StateRepository $stateRepository,
    )
    {
    }

    public function __invoke(SendModuleStateMessage $message)
    {
        $this->persist($message);
        $this->log($message);
    }

    private function persist(SendModuleStateMessage $message)
    {
        $stateHistory = new StateHistory();
        /** @psalm-suppress UndefinedMagicMethod */
        $stateHistory->setModule($this->moduleRepository->findOneByCode($message->moduleCode));
        /** @psalm-suppress UndefinedMagicMethod */
        $stateHistory->setState($this->stateRepository->findOneByCode($message->state));
        $stateHistory->setDatetime((new \DateTime())->setTimestamp($message->timestamp));
        $this->entityManager->persist($stateHistory);
        $this->entityManager->flush();
    }

    private function log(SendModuleStateMessage $message)
    {
        $this->logger->notice(
            'state',
            [
                'module' => $message->moduleCode,
                'timestamp' => $message->timestamp,
                'state' => $message->state,
            ]
        );
    }
}
