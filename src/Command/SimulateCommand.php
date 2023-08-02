<?php

namespace App\Command;

use App\Repository\ModuleRepository;
use App\Simulation\MeasurementGenerator;
use App\Simulation\Model\Event;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsCommand(
    name: 'app:simulate',
    description: 'Add a short description for your command',
)]
class SimulateCommand extends Command
{
    private SymfonyStyle $io;

    private int $tsStart;

    public function __construct(
        private MeasurementGenerator $generator,
        private ModuleRepository $moduleRepository,
        private bool $isRealtime,
        private int $simulationLength,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('length', InputArgument::OPTIONAL, 'Simulation length in seconds', $this->simulationLength)
            ->addOption('no-realtime', 'R', InputOption::VALUE_NONE, 'Force real-time mode OFF' . ($this->isRealtime ? '' : ' (this is by default)'))
            ->addOption('realtime', 'r', InputOption::VALUE_NONE, 'Force real-time mode ON' . ($this->isRealtime ? ' (this is by default)' : ''))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->simulationLength = $input->getArgument('length');

        if ($input->getOption('no-realtime') && !$input->getOption('realtime')) {
            $this->generator->setIsRealtime(false);
            $this->generator->setCurrentTimestamp($this->generator->getCurrentTimestamp() - $this->simulationLength);
        }

        if ($input->getOption('realtime') && !$input->getOption('no-realtime')) {
            $this->generator->setIsRealtime(true);
        }

        $moduleEntities = $this->moduleRepository->findAll();
        foreach ($moduleEntities as $moduleEntity) {
            $this->generator->addModule($moduleEntity);
        }

        $this->tsStart = $this->generator->getCurrentTimestamp();

        $this->generator->start($this->simulationLength);

        $output->writeln('');

        return Command::SUCCESS;
    }

    #[AsEventListener]
    public function logStep(Event $event)
    {
        $this->io->write(sprintf("\r%9.3f of %d sec done | mem: %.2fM", $event->getTimestamp() - $this->tsStart, $this->simulationLength, memory_get_usage(true) / (1<<20)));
    }
}
