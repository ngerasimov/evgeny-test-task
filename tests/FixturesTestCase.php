<?php

namespace Tests;

use App\Entity\MeasuredValue;
use App\Entity\MeasureType;
use App\Entity\Module;
use App\Entity\State;
use App\Entity\StateHistory;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FixturesTestCase extends WebTestCase
{

    /** @var array<string,Module|State|StateHistory|MeasureType|MeasuredValue> */
    protected array $fixtures;

    /**
     * @param array $files
     * @return array<string,Module|State|StateHistory|MeasureType|MeasuredValue>
     * @throws \Nelmio\Alice\Throwable\LoadingThrowable
     */
    protected function loadFixturesFromFiles(array $files): void
    {
        $loader = static::getContainer()->get(NativeLoader::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->purgeDb();
        $this->fixtures = $loader->loadFiles($files)->getObjects();
        foreach ($this->fixtures as $entity) {
            $entityManager->persist($entity);
        }
        $entityManager->flush();
    }

    private function purgeDb()
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get('doctrine.orm.default_entity_manager');
        $entityManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $entityManager->getConnection()->executeQuery('TRUNCATE state_history');
        $entityManager->getConnection()->executeQuery('TRUNCATE measured_value');
        $entityManager->getConnection()->executeQuery('TRUNCATE measure_type_module');
        $entityManager->getConnection()->executeQuery('TRUNCATE module');
        $entityManager->getConnection()->executeQuery('TRUNCATE state');
        $entityManager->getConnection()->executeQuery('TRUNCATE measure_type');
        $entityManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=1');
    }
}