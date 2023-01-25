<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom' , TextType::class)
            ->add('prenom', TextType::class)
            ->add('mail' ,EmailType::class)
            ->add('telephone' , TelType::class)
            ->add('pseudo' , TextType::class)
            ->add('campus', EntityType::class,[
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            //pour la confirmation de motPasse on doit ajouter une autre propriety plaiMotPasse
                //pour quand peut travaillé avec , juste dans la class participant et pas dans la BDD ,
                // par ce que pour la confirmation pas besoin de faire des vérifications dans la BDD
            ->add('plainMotPasse' ,
                RepeatedType::class, [
                    'type' => PasswordType::class,
                       //pour le hachage de mot de passe
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    //pour la confirmation de mot de passe
                    'invalid_message' => 'les mots de passe ne correspondent pas',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options'  => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmation']


            ])
            ->add('Enregistrer' , SubmitType::class,
            [
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->add('Annuler' , ResetType::class,
            [
                'attr' => ['class' => 'btn btn-danger']
            ]);





    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
