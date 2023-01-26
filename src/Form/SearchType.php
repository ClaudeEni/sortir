<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus',EntityType::class,[
                'class'=>Campus::class,
                'choice_label'=>'nom'
            ])
            ->add('nom',TextType::class)
//            ->add('dateDebut',DateType::class,['label'=>'Entre','required'=>'false'])
//            ->add('dateFin',TextType::class,['label'=>'et','format'=>'d-M-yyyy','required'=>'false'])
            ->add('dateDebut',DateType::class,['label'=>'Entre','widget'=>'single_text','format'=>'yyyy-MM-dd','required'=>'false'])
            ->add('dateFin',DateType::class,['label'=>'et','widget'=>'single_text','format'=>'yyyy-MM-dd','required'=>'false'])
            ->add('sortieOrganisateur',CheckboxType::class)
            ->add('sortieInscrit',CheckboxType::class)
            ->add('sortiePasInscrit',CheckboxType::class)
            ->add('sortiePassee',CheckboxType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'attr'=>['novalidate'=>'novalidate']  // pour empêcher la validation du formulaire HTML
        ]);
    }
}
