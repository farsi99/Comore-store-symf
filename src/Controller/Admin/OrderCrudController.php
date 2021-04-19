<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    private $manager;
    private $crudUrlGenerator;

    public function __construct(EntityManagerInterface $manager, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->manager = $manager;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
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
            ChoiceField::new('state', 'Etat de la commande')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3,
                'Terminée' => 4
            ]),
            CollectionField::new('orderDetails', 'Détail Commande')
                ->setTemplatePath('admin/detail-cart.html.twig')
                ->onlyOnDetail()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePrepration = Action::new('updatePrepration', 'Preparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivry = Action::new('updateDelivry', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivry');

        return $actions
            ->add('detail', $updatePrepration)
            ->add('detail', $updateDelivry)
            ->add(Crud::PAGE_INDEX, 'detail');
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->manager->flush();

        $this->addFlash('notice', "<span style='color:green'><strong>La commande " . $order->getReference() . " est bien en cours de préparation</strong></span>");

        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateDelivry(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->manager->flush();

        $this->addFlash('notice', "<span style='color:orange'><strong>La commande " . $order->getReference() . " est en cours de livraison</strong></span>");

        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }
}
