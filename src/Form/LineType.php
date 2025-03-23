<?php

namespace App\Form;

use App\Entity\Dose;
use App\Entity\Line;
use App\Entity\Product;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('qty', NumberType::class, ['attr' => [
                'type' => 'number'
            ]])
            ->add('day', ChoiceType::class, [
                'choices' => [
                    'day.1' => '1',
                    'day.2' => '2',
                    'day.3' => '3',
                    'day.4' => '4',
                    'day.5' => '5',
                    'day.6' => '6',
                    'day.7' => '7',
                    'day.0' => '0',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('dose', EntityType::class, [
                'label' => 'dose',
                'class' => Dose::class
            ])
            /*           ->add('doseNew', DoseType::class, array(
                           'required' => FALSE,
                           'mapped' => FALSE,
                           'property_path' => 'item',
                       )) */
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name'
            ])
          ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Line::class,
            'translation_domain' => 'messages'
        ]);
    }
}
