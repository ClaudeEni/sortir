<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublierSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Bouton "Publier"
            ->add('publier', SubmitType::class, [
                'label' => 'Publier la sortie',
                'attr' => [
                    'class'          => 'btn btn-success'
                ]
            ])
            ->add('annuler', SubmitType::class, [
                'label' => 'Annuler',
                'attr' => [
                    'class'          => 'btn btn-secondary',
                    'formnovalidate' => 'formnovalidate',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
