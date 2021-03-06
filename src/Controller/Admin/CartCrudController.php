<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CartCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cart::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('user.NameComplet', 'Client'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('CarrierPrice', 'Frais de livraison')->setCurrency('EUR'),
            MoneyField::new('subtotalht', 'Total HT')->setCurrency('EUR'),
            MoneyField::new('taxe', 'TVA (20%)')->setCurrency('EUR'),
            MoneyField::new('subtotalttc', 'Total TTC')->setCurrency('EUR'),
            BooleanField::new('isPaid', 'Etat'),
            CollectionField::new('CartDetails', 'Détail panier')
                ->setTemplatePath('admin/detail-cart.html.twig')
                ->onlyOnDetail()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, 'detail');
    }
}
