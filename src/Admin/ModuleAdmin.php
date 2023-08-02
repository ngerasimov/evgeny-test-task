<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Module;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * @template-extends AbstractAdmin<Module>
 */
final class ModuleAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('name')
            ->add('code')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('measureTypes')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('name')
            ->add('code')
            ->add('measureTypes')
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('name')
            ->add('code')
            ->add('measureTypes')
        ;
    }

    /**
     * @param object|Module $object
     * @return void
     */
    protected function preUpdate(object $object): void
    {
        if (!$object instanceof Module) {
            return;
        }

        parent::preUpdate($object);

        foreach ($object->getMeasureTypes() as $measureType) {
            $measureType->addModule($object);
        }
    }
}
