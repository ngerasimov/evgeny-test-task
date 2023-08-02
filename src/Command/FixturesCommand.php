<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fixtures',
    description: 'Load nelmio alice fixtures to your database',
)]
class FixturesCommand extends Command
{
    public function __construct(
        private readonly NativeLoader $loader,
        private EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to YAML file with alice fixtures')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->purgeDb();
        $entities = $this->loader->loadFile($input->getArgument('file'));
        foreach ($entities->getObjects() as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();

        $io->success('Fixtures loaded');

        return Command::SUCCESS;
    }
    
    private function purgeDb()
    {
        $this->entityManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $this->entityManager->getConnection()->executeQuery('TRUNCATE state_history');
        $this->entityManager->getConnection()->executeQuery('TRUNCATE measured_value');
        $this->entityManager->getConnection()->executeQuery('TRUNCATE measure_type_module');
        $this->entityManager->getConnection()->executeQuery('TRUNCATE module');
        $this->entityManager->getConnection()->executeQuery('TRUNCATE state');
        $this->entityManager->getConnection()->executeQuery('TRUNCATE measure_type');
        $this->entityManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=1');
    }
}
