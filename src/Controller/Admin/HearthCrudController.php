<?php

namespace App\Controller\Admin;

use App\Entity\Hearth;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class HearthCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Hearth::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Favoris')
            ->setPageTitle(CRUD::PAGE_INDEX, 'Favoris');
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('author', 'Client'),
            AssociationField::new('product', 'Produit')
        ];
    }
}
