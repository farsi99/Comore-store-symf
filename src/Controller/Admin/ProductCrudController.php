<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom produit'),
            SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex(),
            TextEditorField::new('description'),
            TextEditorField::new('moreInformation')->hideOnIndex(),
            MoneyField::new('price', 'Prix')->setCurrency('EUR'),
            IntegerField::new('quantity', 'Quantité'),
            TextField::new('tags')->hideOnIndex(),
            BooleanField::new('isBesteSeller', 'Meilleurs ventes'),
            BooleanField::new('isNewArrival', 'Arrivages'),
            BooleanField::new('isFeatured', 'Vedette'),
            BooleanField::new('isSepecialOffer', 'Offres spéciales'),
            AssociationField::new('category', 'Catégorie'),
            ImageField::new('image')->setBasePath('/assets/upload/products')
                ->setUploadDir('public/assets/upload/products')
                ->setUploadedFileNamePattern('[randomhash].[extension]'),

        ];
    }
}
