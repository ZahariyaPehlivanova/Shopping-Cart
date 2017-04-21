<?php

namespace AppBundle\Form\Admin;

use AppBundle\Entity\Promotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AddAndEditProductPromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('discount', NumberType::class)
            ->add('duration', DateType::class)
            ->add("products", EntityType::class, [
                "class" => 'AppBundle\Entity\Product',
                "multiple" => true,
                "expanded" => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data-class' => Promotion::class]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_add_promotion_type';
    }
}
