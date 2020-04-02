<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProveedorPagoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('importe',FloatType::class, array(
                    'label' => 'Importe',
                    'attr' => array('size' => 3, 'placeholder' => 'Importe', 'class' => 'importe', 'step' => 0.01),
                    ))
                ->add('ordenPago',HiddenType::class,array('label'=>'Orden de Pago'))
                ->add('pagoTipo', EntityType::class, array(
                    'label' => 'Tipo de Pago',
                    'attr' => array('class' => 'pagoTipo'),
                    'class' => 'AppBundle:PagoTipo',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('banco', EntityType::class, array(
                    'label' => 'Banco',
                    'attr' => array('class' => 'banco', 'disabled' => 'true'),
                    'class' => 'AppBundle:Banco',
                    'required' => false,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('numero',IntegerType::class,array(
                    'label' => 'Número',
                    'attr' => array('size' => 3, 'placeholder' => 'Número', 'class' => 'numero', 'readonly' => 'true'),
                    'required' => false,
                ))
                ->add('fecha',DateType::class,array(
                    'label'=>'Fecha',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => false,
                    'attr' => ['class' => 'js-datepicker fecha', 'autocomplete' => 'off', 'placeholder' => 'Fecha', 'readonly' => 'true']))
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProveedorPago'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ProveedorPagoType';
    }


}
