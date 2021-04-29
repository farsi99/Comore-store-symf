<?php

namespace App\Controller\Admin;

use App\Entity\Alert;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class AlertCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Alert::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC'])
            ->setEntityLabelInSingular('Alerte stock')
            ->setPageTitle(CRUD::PAGE_INDEX, 'Alerte stock');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('product', 'Produit'),
            AssociationField::new('User', 'Client'),
            DateTimeField::new('createdAt', 'Date ajout')
                ->hideOnForm(),
        ];
    }
}
