<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom' )
            ->add('prenom')
            ->add('mail' )
            ->add('telephone' )
            ->add('pseudo' )
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
                    'first_options'  => ['label' => 'Mot de passe','attr' => ['placeholder' => '***********']],
                    'second_options' => ['label' => 'Confirmation']


            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
