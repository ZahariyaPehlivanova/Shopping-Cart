<?php

namespace AppBundle\Form\Admin;

use AppBundle\Entity\Promotion;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class AddAndEditCategoryPromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('discount', NumberType::class)
            ->add('start_date', DateType::class)
            ->add('end_date', DateType::class)
            ->add("categories", EntityType::class, [
                "class" => 'AppBundle\Entity\Category',
                "multiple" => true,
                "expanded" => true,
                "query_builder" => function(EntityRepository $er) {
                    return $er->createQueryBuilder("category")
                        ->where("category.isDeleted = false");
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data-class' => Promotion::class]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_add_and_edit_category_promotion_type';
    }
}
