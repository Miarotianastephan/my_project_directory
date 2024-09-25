<?php

namespace App\Form;

use App\Entity\DetailTransactionCompte;
use App\Entity\PlanCompte;
use App\Entity\TransactionType;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DetailTransactionCompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isTrsDebit', ChoiceType::class, [
                'choices' => [
                    'Débiteur' => true,
                    'Créditeur' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type de transaction'
            ])
            ->add('transaction_type', EntityType::class, [
                'class' => TransactionType::class,
                'choice_label' => function (TransactionType $transactionType) {
                    return $transactionType->getTrsCode() . ' : ' . $transactionType->getTrsLibelle();
                },
                'label' => 'Nature du transaction'
            ])
            ->add('plan_compte', EntityType::class, [
                'class' => PlanCompte::class,
                'choice_label' => function (PlanCompte $planCompte) {
                    return $planCompte->getCptNumero() . ' : ' . $planCompte->getCptLibelle();
                },
                'label' => 'Plan de compte'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DetailTransactionCompte::class,
        ]);
    }
}
