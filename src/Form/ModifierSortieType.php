<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifierSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label'=>'Nom de la sortie'])
            ->add('dateHeureDebut', DateType::class, ['label'=>'Date et heure de la sortie'])
            ->add('dateLimiteInscription', DateType::class, ['label'=>'Date limite d\'inscription'])
            ->add('nbInscriptionsMax', IntegerType::class, ['label'=>'Nombre de places'])
            ->add('duree', IntegerType::class, ['label'=>'Durée'])
            ->add('infosSortie', TextareaType::class, ['label'=>'Description et infos'])
            //->add('campus', EntityType::class, ['label'=>'Campus', 'class'=>Campus::class])
            ->add('ville', EntityType::class, ['label'=>'Ville', 'class'=>Ville::class, 'choice_label'=>'nom', 'mapped'=>false])
            ->add('lieu', EntityType::class, ['label'=>'Lieu', 'class'=>Lieu::class, 'choice_label'=>'nom'])
            //->add('rue', EntityType::class, ['label'=>'Rue', 'class'=>'App\Entity\Lieu', 'choice_label'=>'rue']) TODO: les 4 champs suivants doivent être gérés en front et seront affichés après avoir sélectionné le lieu
            //->add('codePostal', TextType::class, ['label'=>'Code postal'])
            //->add('latitude', TextType::class, ['label'=>'Latitude'])
            //->add('longitude', TextType::class, ['label'=>'Longitude'])
            ->add('enregistrer', SubmitType::class, ['label'=>'Enregistrer'])
            ->add('publier', SubmitType::class, ['label'=>'Publier la sortie'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
