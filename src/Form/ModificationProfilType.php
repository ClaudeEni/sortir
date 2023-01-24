<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModificationProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudonyme', TextType::class, ['label'=>'pseudonyme', 'required'=>false])
            ->add('nom', TextType::class, ['label'=>'nom', 'required'=>false])
            ->add('prenom', TextType::class, ['label'=>'Prenom', 'required'=>false])
            ->add('telephone', TextType::class, ['label'=>'Téléphone', 'required'=>false])
            ->add('mail', TextType::class, ['label'=>'Mail', 'required'=>false])
            ->add('campus',EntityType::class,[
                'class'=>Campus::class,
                'choice_label'=>'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
