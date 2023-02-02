<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnulerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('infosSortie')
            // Bouton "Annuler"
            ->add('save', SubmitType::class, [
                'label' => 'Annuler la sortie',
                'attr' => [
                    'class'          => 'btn btn-danger'
                ]
            ])
            ->add('cancel', SubmitType::class, [
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
