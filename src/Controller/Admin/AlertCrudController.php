<?php

namespace App\Controller\Admin;

use App\Entity\Alert;
use App\Entity\EmailModel;
use App\Services\EmailSender;
use App\Repository\AlertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AlertCrudController extends AbstractCrudController
{

    private $manager;
    private $sendmail;
    private $crudUrlGenerator;

    public function __construct(EntityManagerInterface $manager, EmailSender $sendmail,  CrudUrlGenerator $crudUrlGenerator)
    {
        $this->manager = $manager;
        $this->sendmail = $sendmail;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Alert::class;
    }

    /**
     * @Route(path="/admin/alert/remove", name="remove_alert")
     * @param Request $request
     * @param AlertManager $alertManager
     */
    public function removeAlert(Request $request, AlertRepository $alertRepo)
    {
        $id = $request->query->get('routeParams');
        $alert = $alertRepo->find($id);
        $user = $alert->getUser();
        $product = $alert->getProduct();

        //On envoie un mail à l'utilisateur qui souhaite l'alerte
        $emailMode = new EmailModel();
        $sendmail = $emailMode->setSubject('Alerte produit disponible: #' . $product->getName())
            ->setTitle('Le produit :' . $product->getName() . ' est bien disponible sur votre boutique')
            ->setContent('Bonjour : ' . $user->getLastname() . '<br><br>
           <p> Le produit :' . $product->getName() . ' est déjà disponible sur votre boutique, merci de cliquer sur le lien suivant afin d\'aller voir votre produit
           <a href="">' . $product->getName() . '</a> </p>
           <p>L\'équipe Comores stores vous remercie !</p>');

        $this->sendmail->sendEmailByMailJet($user, $sendmail);
        $this->addFlash('notice', "<span style='color:green'><strong>L'alerte est bien envoyé à : " . $user->getNameComplet() . " </strong></span>");

        //Suppression du produit sur les alertes
        $this->manager->remove($alert);
        $this->manager->flush();

        $url = $this->crudUrlGenerator->build()
            ->setController(AlertCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureActions(Actions $actions): Actions
    {
        $remove = Action::new('removeAlert', 'Alerter', 'fa fa-bell')
            ->setCssClass('btn btn-warning')
            ->linkToRoute('remove_alert', function (Alert $entity) {
                return [
                    'id' => $entity->getId()
                ];
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $remove)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action
                    ->setIcon('fa fa-edit')
                    ->setHtmlAttributes([
                        'title' => 'modifier'
                    ])
                    ->setLabel('')
                    ->setCssClass('btn btn-success');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action
                    ->setIcon('fa fa-trash')
                    ->setHtmlAttributes([
                        'title' => 'supprimer'
                    ])
                    ->setLabel('')
                    ->setCssClass('btn btn-danger');
            });
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
