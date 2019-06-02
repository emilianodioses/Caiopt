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
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ComprobanteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      if ( isset($options['attr']['tipo']) ) {
        if ($options['attr']['tipo'] == 'Compra') {
          $builder->add('tipo', EntityType::class, array(
                        'label' => 'Tipo',
                        'class' => 'AppBundle:AfipComprobanteTipo',
                        'required' => true,
                        'choice_label' => 'descripcion',
                        'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                   return $er->createQueryBuilder('ic')
                                       ->where('ic.activo = 1')
                                       ->andWhere('ic.compra = 1')
                                       ->orderBy('ic.descripcion', 'ASC')
                                       ;
                               }
                  ))
                  ->add('proveedor', Select2EntityType::class, array(
                    'label' => 'Proveedor',
                    'class' => 'AppBundle:Proveedor',
                    'remote_route' => 'proveedor_find_select2',
                    'placeholder' => 'Seleccione un proveedor',
                    'required' => true,
                    'attr' => [
                            'class' => 'proveedor',
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
                  ));
        }
        else {
          $builder->add('tipo', EntityType::class, array(
                        'label' => 'Tipo',
                        'class' => 'AppBundle:AfipComprobanteTipo',
                        'required' => true,
                        'choice_label' => 'descripcion',
                        'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                   return $er->createQueryBuilder('ic')
                                       ->where('ic.activo = 1')
                                       ->andWhere('ic.venta = 1')
                                       ->orderBy('ic.descripcion', 'ASC')
                                       ;
                               }
                  ))
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
                  ->add('cliente', Select2EntityType::class, array(
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
                      ));
        }
      }
      else {
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
                ));
      }
        $builder->add('fecha',DateType::class,array(
                    'label'=>'Fecha',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('puntoVenta', null, array(
                    'label' => 'Punto de Venta',
                    'attr' => array(
                        'readonly' => false,
                    )))
                ->add('numero', IntegerType::class, array(
                    'required' => false,
                    'label' => 'N de Comprobante'))
                ->add('totalBonificacion',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01, 'class' => 'bonificacion'
                    ),
                    'label' => 'Bonificación $'))
                ->add('total',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01
                    ),
                    'label' => 'Total'))
                //->add('caeNumero',HiddenType::class,array('label'=>'CAE Numero'))
                ->add('totalNoGravado',HiddenType::class,array('label'=>'Total no Gravado'))
                ->add('totalNeto',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01
                    ),
                    'label' => 'Total Neto',))
                ->add('importeIvaExento',HiddenType::class,array('label'=>'Importe Exento'))
                ->add('importeIva',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01
                    ),
                    'label' => 'Total IVA'))
                ->add('importeTributos',HiddenType::class, array(
                    'required' => false,
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01
                    ),
                    'label' => 'Importe Tributos'))
                /*->add('importeTributos',FloatType::class, array(
                    'required' => false,
                    'attr' => array(
                        'readonly' => true,
                    ),
                    'label' => 'Importe Tributos'))
                */
                ->add('observaciones',TextareaType::class,array(
                    'label'=>'Observaciones',
                    'required' => false,
                    'attr' => array('rows' => '20')
                ))
                ->add('ordenTrabajo', EntityType::class, array(
                    'label' => 'OT Optica',
                    'class' => 'AppBundle:OrdenTrabajo',
                    'required' => false,
                    'choice_label' => 'id',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->andWhere('l.estado != ?1')
                                   ->setParameter(1, 'Finalizado')
                                   ->orderBy('l.id', 'ASC')
                                   ;
                           }
                ))
                ->add('ordenTrabajoContactologia', EntityType::class, array(
                    'label' => 'OT Contactologia',
                    'class' => 'AppBundle:OrdenTrabajoContactologia',
                    'required' => false,
                    'choice_label' => 'id',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->andWhere('l.estado != ?1')
                                   ->setParameter(1, 'Finalizado')
                                   ->orderBy('l.id', 'ASC')
                                   ;
                           }
                ))
                ->add('movimiento',HiddenType::class,array('label'=>'Movimiento'))
                ->add('condicionVenta', EntityType::class, array(
                    'label' => 'Condición de Venta',
                    'class' => 'AppBundle:AfipCondicionVenta',
                    'required' => true,
                    'choice_label' => 'descripcion',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('ic')
                                   ->where('ic.activo = 1')
                                   ->orderBy('ic.descripcion', 'ASC')
                                   ;
                           }
                ))
                ->add('obraSocialPlan', EntityType::class, array(
                    'label' => 'Obra Social - Plan',
                    'class' => 'AppBundle:ObraSocialPlan',
                    'required' => true,
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('obraSocial',HiddenType::class,array('label'=>'Obra Social'))
                ->add('totalCosto',HiddenType::class,array('label'=>'Total Costo'))
                ->add('totalGanancia',HiddenType::class,array('label'=>'Total Ganancia'))
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
