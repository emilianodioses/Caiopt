<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ComprobanteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tipo', EntityType::class, array(
                    'label' => 'Tipo',
                    'class' => 'AppBundle:AfipComprobanteTipo',
                    'required' => true,
                    'choice_label' => 'descripcion',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('ic')
                                   ->where('ic.activo = 1')
                                   ->orderBy('ic.descripcion', 'ASC')
                                   ;
                           }
                ))
                ->add('fecha',DateType::class,array(
                    'label'=>'Fecha',
                    'widget' => 'single_text',
                    'format' => 'dd-mm-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker']))
                ->add('puntoVenta', null, array(
                    'label' => 'Punto de Venta',
                    'data' => 1,
                    'attr' => array(
                        'readonly' => true,
                    )))
                ->add('numero', IntegerType::class, array(
                    'required' => false,
                    'label' => 'N de Comprobante'))
                ->add('totalBonificacion',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true,
                    ),
                    'attr' => array('class' => 'bonificacion'),
                    'label' => 'Bonificación $'))
                ->add('total',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true,
                    ),
                    'label' => 'Total'))
                ->add('totalNoGravado',HiddenType::class,array('label'=>'Total no Gravado'))
                ->add('totalNeto',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true,
                    ),
                    'label' => 'Total Neto',))
                ->add('importeIvaExento',HiddenType::class,array('label'=>'Importe Exento'))
                ->add('importeIva',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true,
                    ),
                    'label' => 'Total IVA'))
                ->add('importeTributos',FloatType::class, array(
                    'required' => false,
                    'attr' => array(
                        'readonly' => true,
                    ),
                    'label' => 'Importe Tributos'))
                ->add('observaciones',TextareaType::class,array(
                    'label'=>'Observaciones',
                    'required' => false,
                    'attr' => array('rows' => '20')
                ))
                ->add('movimiento',HiddenType::class,array('label'=>'Movimiento'))
                ->add('obraSocialId',HiddenType::class,array('label'=>'Obra Social'))
                ->add('obraSocialPlanId',HiddenType::class,array('label'=>'Plan Obra Social'))
                ->add('totalCosto',HiddenType::class,array('label'=>'Total Costo'))
                ->add('totalGanancia',HiddenType::class,array('label'=>'Total Ganancia'))
                ->add('proveedor', EntityType::class, array(
                    'label' => 'Proveedor',
                    'class' => 'AppBundle:Proveedor',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('cliente', EntityType::class, array(
                    'label' => 'Cliente',
                    'class' => 'AppBundle:Cliente',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('comprobanteDetalles', CollectionType::class, array(
                        'entry_type'   => ComprobanteDetalleType::class,
                        'entry_options' => [
                            'attr' => [
                                'class' => 'item', // we want to use 'tr.item' as collection elements' selector
                            ],
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype'    => true,
                        'required'     => false,
                        'by_reference' => true,
                        'delete_empty' => true,
                        'attr' => [
                            'class' => 'table comprobanteDetalle-collection',
                        ],
                    )
                );
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Comprobante'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ComprobanteType';
    }


}
