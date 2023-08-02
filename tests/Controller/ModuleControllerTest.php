<?php

namespace Tests\Controller;

use App\Entity\Module;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Tests\FixturesTestCase;

class ModuleControllerTest extends FixturesTestCase
{
    private KernelBrowser $client;
    private string $path = '/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Module::class);
        $this->loadFixturesFromFiles(['fixtures/all_test.yaml']);
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Module index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testShow(): void
    {
        $this->loadFixturesFromFiles(['fixtures/all_test.yaml']);

        $this->client->request('GET', sprintf('%s%s/%s', $this->path, $this->fixtures['module.phone']->getId(), $this->fixtures['measure_type.charge_level']->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Module');

        // Use assertions to check that the properties are properly displayed.
    }
}
