<?php

namespace AppBundle\Form;

use AppBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;


class ProductEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('quantity', NumberType::class)
            ->add('image_form', FileType::class,  [
                'data_class' => null,
                'required' => false
            ] )
            ->add('price', MoneyType::class)
            ->add("category", EntityType::class, [
                "class" => 'AppBundle\Entity\Category',
                "placeholder" => 'Select',
                "multiple" => false,

            ])
            ->add("promotions", EntityType::class, [
                "class" => 'AppBundle\Entity\Promotion',
                "multiple" => true,
                "expanded" => true,
                "query_builder" => function (EntityRepository $er) {
                    return $er->createQueryBuilder("promotion")
                        ->where("promotion.isProductPromo = true");
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data-class' => Product::class]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_product_edit_type';
    }
}
