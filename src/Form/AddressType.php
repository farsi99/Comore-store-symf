<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, ['label' => 'Nom de l\'adresse', 'attr' => ['placeholder' => 'exp: Domicile']])
            ->add('campany', TextType::class, [
                'label' => 'Société',
                'attr' => ['placeholder' => 'nom de la société'],
                'required' => false
            ])
            ->add('address', TextareaType::class, ['label' => 'adresse', 'attr' => ['placeholder' => 'n° et rue']])
            ->add('complement', TextareaType::class, [
                'label' => 'Complement adresse',
                'attr' => ['placeholder' => 'suite de l\'adresse'],
                'required' => false
            ])
            ->add('phone', TelType::class, ['label' => 'Téléphone', 'attr' => ['placeholder' => 'mon téléphone']])
            ->add('city', TextType::class, ['label' => 'Ville', 'attr' => ['placeholder' => 'ma ville']])
            ->add('codePostal', TextType::class, ['label' => 'Code postal', 'attr' => ['placeholder' => 'mon code postal']])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'preferred_choices' => ['Comores', 'France']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
