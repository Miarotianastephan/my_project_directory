<?php

namespace App\Form;

use App\Entity\Banque;
use App\Entity\Chequier;
use App\Type\CustomDateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ChequierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('chequier_numero_debut', TextType::class, [
                'label' => 'Numero de debut'
            ])
            ->add('chequier_numero_fin', TextType::class, [
                'label' => 'Numero de fin'
            ])
            ->add('chequier_date_arrivee', DateType::class, [
                'label' => 'Date d\'arrivée',
                'data' => new \DateTime('now'),
                'widget' => 'single_text', // Utilise un champ de type text avec un format de date unique
                'format' => 'yyyy-MM-dd',  // Format de la date
                'html5' => true,          // Pour permettre une personnalisation de l'affichage
            ])

            ->add('banque', EntityType::class, [
                'class' => Banque::class,
                'choice_label' => 'nomBanque',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chequier::class,
        ]);
    }
}
