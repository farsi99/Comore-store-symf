<?php

namespace App\Controller\Admin;

use App\Entity\EmailModel;
use App\Entity\Order;
use App\Services\EmailSender;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
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
    private $emailsend;

    public function __construct(EntityManagerInterface $manager, EmailSender $emailsend, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->manager = $manager;
        $this->crudUrlGenerator = $crudUrlGenerator;
        $this->emailsend = $emailsend;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setPageTitle(Crud::PAGE_INDEX, 'Commandes');
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
                'Non pay??e' => 0,
                'Pay??e' => 1,
                'Pr??paration en cours' => 2,
                'Livraison en cours' => 3,
                'Termin??e' => 4
            ])->setCssClass('alert alert-info'),
            CollectionField::new('orderDetails', 'D??tail Commande')
                ->setTemplatePath('admin/detail-cart.html.twig')
                ->onlyOnDetail()
        ];
    }


    public function configureActions(Actions $actions): Actions
    {
        $updatePrepration = Action::new('updatePrepration', 'Preparation en cours', 'fas fa-box-open')
            ->linkToCrudAction('updatePreparation')
            ->setCssClass('btn btn-info');
        $updateDelivry = Action::new('updateDelivry', 'Livraison en cours', 'fas fa-truck')
            ->linkToCrudAction('updateDelivry')
            ->setCssClass('btn btn-success');

        return $actions
            ->add('detail', $updatePrepration)
            ->add('detail', $updateDelivry)
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
                    ->setIcon('fas fa-edit')
                    ->setLabel('')
                    ->setCssClass('btn btn-success');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action
                    ->setIcon('fas fa-trash')
                    ->setLabel('')
                    ->setCssClass('btn btn-danger');
            })
            ->disable(Action::NEW);
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->manager->flush();
        //Envoie d'email pour prevenir que le colis est en preprartion
        $emailMode = new EmailModel();
        $sendmail = $emailMode->setSubject('Commande reference: #' . $order->getReference())
            ->setTitle('La pr??paration de votre commande est en cours')
            ->setContent('Bonjour : ' . $order->getUser()->getLastname() . '<br><br>
           <p> Votre commande n?? #' . $order->getReference() . ' est en cours de pr??paration </p>
           <p>L\'??quipe Comores stores vous remercie !</p>');

        $this->emailsend->sendEmailByMailJet($order->getUser(), $sendmail);
        $this->addFlash('notice', "<span style='color:green'><strong>La commande " . $order->getReference() . " est bien en cours de pr??paration</strong></span>");

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

        //Envoie de message  
        $emailMode = new EmailModel();
        $sendmail = $emailMode->setSubject('Commande reference: #' . $order->getReference())
            ->setTitle('La livraison de votre commande est en cours')
            ->setContent('Bonjour : ' . $order->getUser()->getLastname() . '<br><br>
           <p> Votre commande n?? #' . $order->getReference() . ' est en cours de livraison </p>
           <p>L\'??quipe Comores stores vous remercie !</p>');

        $this->emailsend->sendEmailByMailJet($order->getUser(), $sendmail);
        $this->addFlash('notice', "<span style='color:orange'><strong>La commande " . $order->getReference() . " est en cours de livraison</strong></span>");

        $url = $this->crudUrlGenerator->build()
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }
}
