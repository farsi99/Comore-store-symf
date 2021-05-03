<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class BlogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Blog::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            SlugField::new('slug', 'Slug')->setTargetFieldName('title')->hideOnIndex(),
            TextareaField::new('resume')->hideOnIndex(),
            TextEditorField::new('content', 'Contenue')->setNumOfRows(12),
            ImageField::new('image', 'Image')->setBasePath('/assets/upload/articles')
                ->setUploadDir('public/assets/upload/articles')
                ->setUploadedFileNamePattern('[randomhash].[extension]'),
            BooleanField::new('ordreAffichage', 'Mettre en avant'),
            BooleanField::new('publication', 'Publier'),
            AssociationField::new('categorie', 'Categorie')
        ];
    }
}
