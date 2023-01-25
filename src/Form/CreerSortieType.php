<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,['label'=>'Nom de la sortie:'])
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
           ->add('campus',EntityType::class,['class'=>Campus::class,'choice_label'=>'nom','disabled'=>true])
            //->add('campus',TextType::class,['label'=>'campus','disabled'=>true])
            ->add('lieu',EntityType::class,['class'=>Lieu::class,'choice_label'=>'nom'])
            //->add('etat',EntityType::class,['label'=>'Etat:','class'=>Etat::class,'choice_label'=>'libelle'])
           // ->add('organisateur',EntityType::class,['label'=>'organisateur:','class'=>Participant::class,'choice_label'=>'id'])
            //->add('participants')
            ->add('latitude',NumberType::class,['label'=>'Latitude:','mapped'=>false,'required'=>false])
             ->add('longitude',NumberType::class,['label'=>'Longitude:','mapped'=>false,'required'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
