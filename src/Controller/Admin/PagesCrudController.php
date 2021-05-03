<?php

namespace App\Controller\Admin;

use App\Entity\Pages;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class PagesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pages::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(CRUD::PAGE_INDEX, 'Page');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, 'detail')
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action
                    ->setIcon('fa fa-eye')
                    ->setHtmlAttributes([
                        'title' => 'voir'
                    ])
                    ->setLabel('')
                    ->setCssClass('btn btn-info');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action
                    ->setIcon('fa fa-edit')
                    ->setCssClass('btn btn-success')
                    ->setLabel('')
                    ->setHtmlAttributes([
                        'title' => 'Modifier'
                    ]);
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action
                    ->setIcon('fa fa-trash')
                    ->setLabel('')
                    ->setCssClass(' btn btn-danger action-delete')
                    ->setHtmlAttributes([
                        'title' => 'supprimer'
                    ]);
            })

            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action
                    ->setLabel('Créer une page')
                    ->setIcon('fas fa-plus-circle')
                    ->setHtmlAttributes(['title' => 'Créer une page']);
            });
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            SlugField::new('slug')->setTargetFieldName('title'),
            TextField::new('meta_title', 'Titre SEO')->hideOnIndex(),
            TextareaField::new('meta_description', 'Description SEO')->hideOnIndex(),
            TextEditorField::new('content', 'Description')->setNumOfRows(14),
            ImageField::new('image')->setBasePath('/assets/upload/articles')
                ->setUploadDir('public/assets/upload/articles')
                ->setUploadedFileNamePattern('[randomhash].[extension]'),
            BooleanField::new('affichage_menu', 'Afficher dans le menu'),
            TextField::new('menu', 'Libellé menu'),
            SlugField::new('slugMenu')->setTargetFieldName('menu')->hideOnIndex()

        ];
    }
}
