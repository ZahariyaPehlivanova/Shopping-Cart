<?php

namespace AppBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChangeOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderBy', ChoiceType::class, [
                "placeholder" => "Order by",
                "choices" => [
                    "addedOn" => "addedOn",
                    "rating" => "rating",
                    "price" => "price",
                ]
            ])
            ->add('criteria', ChoiceType::class, [
                "placeholder" => "Sorting criteria",
                "choices" => [
                    "ASC" => "ASC",
                    "DESC" => "DESC"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'app_bundle_change_order_type';
    }
}
