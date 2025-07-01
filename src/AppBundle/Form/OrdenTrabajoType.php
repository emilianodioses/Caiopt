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
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;


class OrdenTrabajoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $estados = array(
            'Pendiente' => 'Pendiente',
            'Nuevo' => 'Nuevo',
            'Enviado' => 'Enviado',
            'Finalizado' => 'Finalizado');

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
                ->add('fechaRecepcion',DateType::class,array(
                    'label'=>'Fecha Recepcion',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('fechaEntrega',DateType::class,array(
                    'label'=>'Fecha Entrega',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => false,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))

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
                ->add('fechaReceta',DateType::class,array(
                    'label'=>'Fecha Receta',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => false,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('estado',ChoiceType::class,array(
                        'label'=>'Estado',
                        'choices' => $estados,
                            'choices_as_values' => true))
                ->add('observaciones',TextareaType::class,array(
                    'label'=>'Observaciones',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')
                ))
                ->add('taller', EntityType::class, array(
                    'label' => 'Laboratorio',
                    'class' => 'AppBundle:Taller',
                    'required' => false,
                    'choice_label' => function ($taller) {
                        return $taller->getNumero().' - '.$taller->getNombre();
                    },
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('numeroTaller', IntegerType::class, array(
                    'label' => 'Número',
                    'required' => false,))
                ->add('fechaTallerPedido',DateType::class,array(
                    'label'=>'Fecha Pedido',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => false,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('fechaTallerEntrega',DateType::class,array(
                    'label'=>'Fecha Entrega',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => false,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('armado', null, array('label' => 'Armado','required' => false,))
            ->add('armazon_propio', null, array('label' => 'Armazon Propio','required' => false,))
                ->add('otrosTrabajos', null, array('label' => 'Otros Trabajos','required' => false,))
                ->add('total',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01
                    ),
                    'label' => 'Total'))
                ->add('totalBonificacion',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01, 'class' => 'bonificacion'
                    ),
                    'label' => 'Bonificación $'))
                ->add('entrega',FloatType::class, array(
                    'attr' => array(
                        'readonly' => false, 'step' => 0.01
                    ),
                    'label' => 'Entrega'))
                ->add('saldo',FloatType::class, array(
                    'attr' => array(
                        'readonly' => true, 'step' => 0.01
                    ),
                    'label' => 'Saldo'))
                ->add('lejosOjoDerechoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'lejosOjoDerechoEje',
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Derecho'))
                ->add('lejosOjoIzquierdoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'lejosOjoIzquierdoEje',
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Izquierdo'))
                ->add('lejosOjoDerechoCilindro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'lejosOjoDerechoCilindro',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Cilindro Ojo Derecho'))
                ->add('lejosOjoIzquierdoCilindro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'lejosOjoIzquierdoCilindro',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Cilindro Ojo Izquierdo'))
                ->add('lejosOjoDerechoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'lejosOjoDerechoEsfera',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('lejosOjoIzquierdoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'lejosOjoIzquierdoEsfera',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                    ->add('cercaOjoDerechoEje',IntegerType::class, array(
                          'attr' => array(
                          'required' => true,
                          'class' => 'cercaOjoDerechoEje',
                          'empty_data' => 0,
                          ),
                          'label' => 'Eje Ojo Derecho'))
                      ->add('cercaOjoIzquierdoEje',IntegerType::class, array(
                          'attr' => array(
                          'required' => true,
                          'class' => 'cercaOjoIzquierdoEje',
                          'empty_data' => 0,
                          ),
                          'label' => 'Eje Ojo Izquierdo'))
                      ->add('cercaOjoDerechoCilindro',FloatType::class, array(
                          'attr' => array(
                          'required' => true,
                          'class' => 'cercaOjoDerechoCilindro',
                          'empty_data' => 0,
                          'step' => 0.25,
                          ),
                          'label' => 'Cilindro Ojo Derecho'))
                      ->add('cercaOjoIzquierdoCilindro',FloatType::class, array(
                          'attr' => array(
                          'required' => true,
                          'class' => 'cercaOjoIzquierdoCilindro',
                          'empty_data' => 0,
                          'step' => 0.25,
                          ),
                          'label' => 'Cilindro Ojo Izquierdo'))
                ->add('cercaOjoDerechoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'cercaOjoDerechoEsfera',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('cercaOjoIzquierdoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'cercaOjoIzquierdoEsfera',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                ->add('ojoDerechoDnp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'ojoDerechoDnp',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'DNP OD'))
                ->add('ojoIzquierdoDnp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'ojoIzquierdoDnp',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'DNP OI'))
                ->add('ojoDerechoHp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'ojoDerechoHp',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'HP OD'))
                ->add('ojoIzquierdoHp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'ojoIzquierdoHp',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'HP OI'))
                ->add('ojoDerechoHpu',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'ojoDerechoHpu',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'HPU OD'))
                ->add('ojoIzquierdoHpu',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'ojoIzquierdoHpu',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'HPU OI'))
                ->add('antesLejosOjoDerechoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesLejosOjoDerechoEje',
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Derecho'))
                ->add('antesLejosOjoIzquierdoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesLejosOjoIzquierdoEje',
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Izquierdo'))
                ->add('antesLejosOjoDerechoCilindro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesLejosOjoDerechoCilindro',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Cilindro Ojo Derecho'))
                ->add('antesLejosOjoIzquierdoCilindro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesLejosOjoIzquierdoCilindro',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Cilindro Ojo Izquierdo'))
                ->add('antesLejosOjoDerechoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesLejosOjoDerechoEsfera',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('antesLejosOjoIzquierdoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesLejosOjoIzquierdoEsfera',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                    ->add('antesCercaOjoDerechoEje',IntegerType::class, array(
                        'attr' => array(
                        'required' => true,
                        'class' => 'antesCercaOjoDerechoEje',
                        'empty_data' => 0,
                        ),
                        'label' => 'Eje Ojo Derecho'))
                    ->add('antesCercaOjoIzquierdoEje',IntegerType::class, array(
                        'attr' => array(
                        'required' => true,
                        'class' => 'antesCercaOjoIzquierdoEje',
                        'empty_data' => 0,
                        ),
                        'label' => 'Eje Ojo Izquierdo'))
                    ->add('antesCercaOjoDerechoCilindro',FloatType::class, array(
                        'attr' => array(
                        'required' => true,
                        'class' => 'antesCercaOjoDerechoCilindro',
                        'empty_data' => 0,
                        'step' => 0.25,
                        ),
                        'label' => 'Cilindro Ojo Derecho'))
                    ->add('antesCercaOjoIzquierdoCilindro',FloatType::class, array(
                        'attr' => array(
                        'required' => true,
                        'class' => 'antesCercaOjoIzquierdoCilindro',
                        'empty_data' => 0,
                        'step' => 0.25,
                        ),
                        'label' => 'Cilindro Ojo Izquierdo'))

                ->add('antesCercaOjoDerechoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesCercaOjoDerechoEsfera',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('antesCercaOjoIzquierdoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesCercaOjoIzquierdoEsfera',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                ->add('antesOjoDerechoDnp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesOjoDerechoDnp',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'DNP OD'))
                ->add('antesOjoIzquierdoDnp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesOjoIzquierdoDnp',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'DNP OI'))
                ->add('antesOjoDerechoHp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesOjoDerechoHp',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'HP OD'))
                ->add('antesOjoIzquierdoHp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesOjoIzquierdoHp',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'HP OI'))
                ->add('antesOjoDerechoHpu',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesOjoDerechoHpu',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'HPU OD'))
                ->add('antesOjoIzquierdoHpu',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'class' => 'antesOjoIzquierdoHpu',
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'HPU OI'))
                ->add('ordenTrabajoDetalles', CollectionType::class, array(
                        'entry_type'   => OrdenTrabajoDetalleType::class,
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
                            'class' => 'table ordenTrabajoDetalle-collection',
                        ],
                    )
                )
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
                  ));

                /* FALLA EN PRODUCCION, NO TENGO IDEA DE PORQUE
                ->add('comprobante', EntityType::class, array(
                    'label' => 'Comprobante',
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
                */
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrdenTrabajo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'OrdenTrabajoType';
    }


}
