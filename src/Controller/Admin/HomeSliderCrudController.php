<?php

namespace App\Controller\Admin;

use App\Entity\HomeSlider;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class HomeSliderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return HomeSlider::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextField::new('description'),
            TextField::new('buttonMessage', 'Message du bouton'),
            TextField::new('buttonUrl', 'Lien du slide'),
            ImageField::new('image')->setBasePath('/assets/upload/slider')
                ->setUploadDir('public/assets/upload/slider')
                ->setUploadedFileNamePattern('[randomhash].[extension]'),
            BooleanField::new('isDisplayed', 'Publié')
        ];
    }
}
