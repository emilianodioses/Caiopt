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

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ComprobanteType extends AbstractType
{
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $comprobanteDetallesTipo = '';

      if ( isset($options['attr']['tipo']) ) {
        $comprobanteDetallesTipo = $options['attr']['tipo'] == 'Compra';
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
                    ->add('numero', IntegerType::class, array(
                        'required' => true,
                        'label' => 'N de Comprobante'))
                    ->add('puntoVenta', null, array(
                        'label' => 'Punto de Venta',
                        'attr' => array(
                            'readonly' => false,
                        )));
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
                               },
//                        'data' => $this->getDoctrine()->getEntityManager()
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
                      ))
                  ->add('numero', IntegerType::class, array(
                    'required' => false,
                    'label' => 'N de Comprobante'))
                  ->add('puntoVentaId', EntityType::class, array(
                        'label' => 'Punto de Venta',
                        'class' => 'AppBundle:PuntoVenta',
                        'required' => true,
                        'choice_label' => 'numero',
                        'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                   return $er->createQueryBuilder('ic')
                                       ->where('ic.activo = 1')
                                       ->andWhere('ic.sucursal = ?1')
                                       ->orderBy('ic.numero', 'ASC')
                                       ->setParameter(1, $this->user->getSucursal()->getId())
                                       ;
                               }
                  ))
                  ->add('usuario', EntityType::class, array(
                        'label' => 'Vendedor',
                        'class' => 'AppBundle:Usuario',
                        'required' => true,
                        'choice_label' => 'usuario',
                        'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                   return $er->createQueryBuilder('ic')
                                       ->where('ic.activo = 1')
                                       ->orderBy('ic.usuario', 'ASC')
                                       ;
                               }
                  ))
                  ->add('comprobanteAsociado', EntityType::class, array(
                        'label' => 'Comprobante Asociado',
                        'class' => 'AppBundle:Comprobante',
                        'required' => false,
                        'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                   return $er->createQueryBuilder('l')
                                       ->where('l.activo = 1')
                                       ->andWhere('l.movimiento = ?1')
                                       ->setParameter(1, 'Venta')
                                       ->orderBy('l.id', 'DESC')
                                       ->setMaxResults(100)
                                       ;
                               }
                    ))
                  ->add('medico', Select2EntityType::class, array(
                      'label' => 'Medico',
                      'class' => 'AppBundle:Medico',
                      'remote_route' => 'medico_find_select2',
                      'placeholder' => 'Seleccione un Medico',
                      'required' => false,
                      'attr' => [
                              'class' => 'medico',
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
                  ->add('ordenTrabajo', Select2EntityType::class, array(
                      'label' => 'OT Optica',
                      'class' => 'AppBundle:OrdenTrabajo',
                      'remote_route' => 'ordentrabajo_find_select2',
                      'placeholder' => 'Seleccione una orden de trabajo',
                      'required' => false,
                      'attr' => [
                          'class' => 'ordentrabajo',
                      ],
                      'primary_key' => 'id',
                      'text_property' => 'comprobante',
                      'minimum_input_length' => 2,
                      'page_limit' => 10,
                      'allow_clear' => false,
                      'delay' => 250,
                      'cache' => true,
                      'cache_timeout' => 60000, // if 'cache' is true
                      'language' => 'es',
                  ))
                  ->add('ordenTrabajoContactologia', Select2EntityType::class, array(
                      'label' => 'OT Contactologia',
                      'class' => 'AppBundle:OrdenTrabajoContactologia',
                      'remote_route' => 'ordentrabajocontactologia_find_select2',
                      'placeholder' => 'Seleccione una orden de trabajo contactologia',
                      'required' => false,
                      'attr' => [
                          'class' => 'ordentrabajocontactologia',
                      ],
                      'primary_key' => 'id',
                      'text_property' => 'comprobante',
                      'minimum_input_length' => 2,
                      'page_limit' => 10,
                      'allow_clear' => false,
                      'delay' => 250,
                      'cache' => true,
                      'cache_timeout' => 60000, // if 'cache' is true
                      'language' => 'es',
                  ))
                  ->add('obraSocialPlan', EntityType::class, array(
                      'label' => 'Obra Social - Plan',
                      'class' => 'AppBundle:ObraSocialPlan',
                      'required' => true,
                      'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                 return $er->createQueryBuilder('l')
                                     ->where('l.activo = 1')
                                     ->leftJoin('l.obraSocial', 'os')
                                     ->orderBy('os.nombre', 'ASC')
                                     ->addOrderBy('l.nombre', 'ASC');
                                     ;
                             }
                  ))
                  ->add('obraSocial',HiddenType::class,array('label'=>'Obra Social'));
          if ( isset($options['attr']['op']) ) {
            if ($options['attr']['op'] == 'New') {
              $builder->add('clientePagos', CollectionType::class, array(
                    'mapped' => false,
                    'entry_type'   => ClientePagoType::class,
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
                    'required' => true,
                    'attr' => [
                        'class' => 'table clientePago-collection',
                    ],
                ));
            }
          }
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
                ))
                ->add('numero', IntegerType::class, array(
                    'required' => true,
                    'label' => 'N de Comprobante'))
                ->add('puntoVenta', null, array(
                    'label' => 'Punto de Venta',
                    'attr' => array(
                        'readonly' => false,
                    )));
      }
        
        $builder->add('fecha',DateType::class,array(
                    'label'=>'Fecha',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
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
                ->add('movimiento',HiddenType::class,array('label'=>'Movimiento'))
                ->add('totalCosto',HiddenType::class,array('label'=>'Total Costo'))
                ->add('totalGanancia',HiddenType::class,array('label'=>'Total Ganancia'))
                ->add('comprobanteDetalles', CollectionType::class, array(
                        'entry_type'   => ComprobanteDetalleType::class,
                        'entry_options' => [
                            'attr' => [
                                'class' => 'item', // we want to use 'tr.item' as collection elements' selector
                                'tipo' => $comprobanteDetallesTipo,
                            ],
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype'    => true,
                        'required'     => true,
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
