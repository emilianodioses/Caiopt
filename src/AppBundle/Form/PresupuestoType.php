<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\Range;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;


class PresupuestoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $porcentajeIva = array(
            '0.00' => '0.00',
            '10.50' => '10.50',
            '21.00' => '21.00'
        );
        $builder->add('cliente', Select2EntityType::class, array(
                    'label' => 'Cliente',
                    'class' => 'AppBundle:Cliente',
                    'remote_route' => 'cliente_find_select2',
                    'placeholder' => 'Seleccione un cliente',
                    'required' => true,
                    'attr' => [
                        'class' => 'cliente',
                    ],
                    'primary_key' => 'id',
                    'text_property' => 'nombre',
                    'minimum_input_length' => 2,
                    'page_limit' => 10,
                    'allow_clear' => false,
                    'delay' => 250,
                    'cache' => true,
                    'cache_timeout' => 60000, // if 'cache' is true
                    'language' => 'es',
                     ))
                ->add('fechaPresup',DateType::class,array(
                    'label'=>'Fecha Presupuesto',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('plazoEntrega', IntegerType::class, array(
                    'required' => true,
                    'label' => 'Plazo Entrega (Días hábiles)'))
                ->add('validezPresupuesto', IntegerType::class, array(
                    'required' => true,
                    'label' => 'Validez presupuesto (en días)'))
                ->add('retiro',TextareaType::class,array(
                    'label'=>'Sucursal de retiro',
                    'required' => false,
                    'empty_data' => '',
                    'disabled' => true,
                    'attr' => array('rows' => '1')))
                ->add('presupuestoDetalles', CollectionType::class, array(
                        'entry_type'   => PresupuestoDetalleType::class,
                        'entry_options' => [
                            'attr' => [
                                'class' => 'item', // we want to use 'tr.item' as collection elements' selector
                            ],
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype'    => true,
                        'required'     => true,
                        'by_reference' => true,
                        'delete_empty' => true,
                        'attr' => [
                            'class' => 'table presupuestoDetalle-collection',
                        ],
                    )
                )
                ->add('moneda',TextareaType::class,array(
                    'label'=>'Moneda',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')))
                ->add('total',FloatType::class,array(
                    'label'=>'Subtotal',
                    'attr' => array('readonly' => true, 'size' => 3, 'placeholder' => 'Subtotal', 'class' => 'total', 'step' => 0.01),
                ))
                ->add('totalBonificacion',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01, 'class' => 'bonificacion'
                    ),
                    'label' => 'Bonificación $'))
                ->add('iva21',ChoiceType::class,array(
                    'label'=>'Iva',
                    'choices' => $porcentajeIva,
                    'attr' => array('placeholder' => 'Iva', 'class' => 'iva21', 'step' => 0.01),
                    'choices_as_values' => true))
                ->add('totalPresupuesto',FloatType::class,array(
                    'label'=>'Total Presupuesto',
                    'attr' => array('readonly' => true, 'size' => 3, 'placeholder' => 'Total Presupuesto', 'class' => 'totalPresupuesto', 'step' => 0.01),
                ))
                ->add('observaciones',TextareaType::class,array(
                    'label'=>'Observaciones',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')
                ));

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Presupuesto'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'PresupuestoType';
    }


}
