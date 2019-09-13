<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class LibroCajaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fecha',DateType::class,array(
                    'label'=>'Fecha',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('saldoInicial',FloatType::class, array(
                    'attr' => array(
                        'step' => 0.01, 'class' => 'saldoInicial'
                    ),
                    'required' => true,
                    'label' => 'Saldo Inicial'))
                ->add('saldoFinal',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01, 'class' => 'saldoFinal'
                    ),
                    'required' => true,
                    'label' => 'Saldo Final'))
                ->add('caja',FloatType::class, array(
                    'attr' => array(
                        'step' => 0.01, 'class' => 'caja'
                    ),
                    'required' => true,
                    'label' => 'Caja'))
                ->add('diferencia',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01, 'class' => 'diferencia'
                    ),
                    'required' => true,
                    'label' => 'Diferencia'))
                ;

                /*
                ->add('createdBy')
                ->add('createdAt')
                ->add('updatedBy')
                ->add('updatedAt')
                ->add('activo')
                ->add('sucursal');
                */
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\LibroCaja'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_librocaja';
    }


}
