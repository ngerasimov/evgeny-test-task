<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Module;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template-extends CRUDController<Module>
 */
final class ModuleAdminController extends CRUDController
{
    protected function preEdit(Request $request, object $object): ?Response
    {
        if ($object instanceof Module) {
            foreach ($object->getMeasureTypes() as $measureType) {
                $measureType->removeModule($object);
            }
        }
        return parent::preEdit($request, $object);
    }
}
