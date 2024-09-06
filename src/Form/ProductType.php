<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'form.product.name',
                'attr' => [
                    'placeholder' => 'form.name.placeholder',
                ]
            ])
            ->add('comment', TextType::class, [
                'translation_domain' => 'messages',
                'label' => 'form.product.comment',
                'required' => false,
                'attr' => [
                    'placeholder' => 'form.name.comment',
                ]
            ])//->add('envoi', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
