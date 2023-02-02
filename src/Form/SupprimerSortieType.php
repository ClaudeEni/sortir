<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupprimerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Bouton "Supprimer"
            ->add('supprimer', SubmitType::class, [
                'label' => 'Supprimer la sortie',
                'attr' => [
                    'class'          => 'btn btn-danger'
                ]
            ])
            ->add('annuler', SubmitType::class, [
                'label' => 'Retour',
                'attr' => [
                    'class'          => 'btn btn-warning',
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
