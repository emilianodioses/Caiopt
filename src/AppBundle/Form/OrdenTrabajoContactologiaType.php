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

class OrdenTrabajoContactologiaType extends AbstractType
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
                ->add('estado',ChoiceType::class,array(
                        'label'=>'Estado',
                        'choices' => $estados,
                            'choices_as_values' => true))
                ->add('diagnostico',TextareaType::class,array(
                    'label'=>'Diagnostico',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')
                ))
                ->add('rp',TextareaType::class,array(
                    'label'=>'R/P',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')
                ))
                ->add('observaciones',TextareaType::class,array(
                    'label'=>'Observaciones',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')
                ))
                ->add('motivacion',TextareaType::class,array(
                    'label'=>'Motivacion',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')
                ))
                ->add('usoLC',TextareaType::class,array(
                    'label'=>'Uso L.C',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')
                ))
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
                ->add('rcOjoDerechoHorizontal',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 1,
                    ),
                    'label' => 'RC Ojo Derecho Horizontal'))
                ->add('rcOjoIzquierdoHorizontal',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 1,
                    ),
                    'label' => 'RC Ojo Izquierdo Horizontal'))
                ->add('rcOjoDerechoVertical',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 1,
                    ),
                    'label' => 'RC Ojo Derecho Vertical'))
                ->add('rcOjoIzquierdoVertical',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 1,
                    ),
                    'label' => 'RC Ojo Izquierdo Vertical'))
                ->add('ojoDerechoCurvas',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.10,
                    ),
                    'label' => 'Ojo Izquierdo Curvas'))
                ->add('ojoIzquierdoCurvas',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.10,
                    ),
                    'label' => 'Ojo Derecho Curvas'))
                ->add('ojoIzquierdoDiametro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.10,
                    ),
                    'label' => 'Ojo Izquierdo Diametro'))
                ->add('ojoDerechoDiametro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.10,
                    ),
                    'label' => 'Ojo Derecho Diametro'))
                ->add('ojoDerechoAV',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Ojo Derecho AV'))
                ->add('ojoIzquierdoAV',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Ojo Izquierdo AV'))
                ->add('ojoDerechoCaracteristicas',TextareaType::class,array(
                    'label'=>'Motivacion',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')
                ))
                ->add('ojoIzquierdoCaracteristicas',TextareaType::class,array(
                    'label'=>'Motivacion',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '1')
                ))
                ->add('lejosOjoDerechoEje',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Eje Ojo Derecho'))
                ->add('lejosOjoIzquierdoEje',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Eje Ojo Izquierdo'))
                ->add('lejosOjoDerechoCilindro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Cilindro Ojo Derecho'))
                ->add('lejosOjoIzquierdoCilindro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Cilindro Ojo Izquierdo'))
                ->add('lejosOjoDerechoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('lejosOjoIzquierdoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                ->add('cercaOjoDerechoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('cercaOjoIzquierdoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                    ->add('cercaOjoDerechoEje',FloatType::class, array(
                        'attr' => array(
                        'required' => true,
                        'empty_data' => 0,
                        'step' => 0.25,
                        ),
                        'label' => 'Eje Ojo Derecho'))
                    ->add('cercaOjoIzquierdoEje',FloatType::class, array(
                        'attr' => array(
                        'required' => true,
                        'empty_data' => 0,
                        'step' => 0.25,
                        ),
                        'label' => 'Eje Ojo Izquierdo'))
                    ->add('cercaOjoDerechoCilindro',FloatType::class, array(
                        'attr' => array(
                        'required' => true,
                        'empty_data' => 0,
                        'step' => 0.25,
                        ),
                        'label' => 'Cilindro Ojo Derecho'))
                    ->add('cercaOjoIzquierdoCilindro',FloatType::class, array(
                        'attr' => array(
                        'required' => true,
                        'empty_data' => 0,
                        'step' => 0.25,
                        ),
                        'label' => 'Cilindro Ojo Izquierdo'))
                ->add('ojoDerechoDnp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'DNP OD'))
                ->add('ojoIzquierdoDnp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'DNP OI'))
                ->add('antesLejosOjoDerechoEje',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Eje Ojo Derecho'))
                ->add('antesLejosOjoIzquierdoEje',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Eje Ojo Izquierdo'))
                ->add('antesLejosOjoDerechoCilindro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Cilindro Ojo Derecho'))
                ->add('antesLejosOjoIzquierdoCilindro',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Cilindro Ojo Izquierdo'))
                ->add('antesLejosOjoDerechoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('antesLejosOjoIzquierdoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                    ->add('antesCercaOjoDerechoEje',FloatType::class, array(
                      'attr' => array(
                      'required' => true,
                      'empty_data' => 0,
                      'step' => 0.25,
                      ),
                      'label' => 'Eje Ojo Derecho'))
                  ->add('antesCercaOjoIzquierdoEje',FloatType::class, array(
                      'attr' => array(
                      'required' => true,
                      'empty_data' => 0,
                      'step' => 0.25,
                      ),
                      'label' => 'Eje Ojo Izquierdo'))
                  ->add('antesCercaOjoDerechoCilindro',FloatType::class, array(
                      'attr' => array(
                      'required' => true,
                      'empty_data' => 0,
                      'step' => 0.25,
                      ),
                      'label' => 'Cilindro Ojo Derecho'))
                  ->add('antesCercaOjoIzquierdoCilindro',FloatType::class, array(
                      'attr' => array(
                      'required' => true,
                      'empty_data' => 0,
                      'step' => 0.25,
                      ),
                      'label' => 'Cilindro Ojo Izquierdo'))
                ->add('antesCercaOjoDerechoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('antesCercaOjoIzquierdoEsfera',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                ->add('antesOjoDerechoDnp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'DNP OD'))
                ->add('antesOjoIzquierdoDnp',FloatType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                    ),
                    'label' => 'DNP OI'))
                ->add('ordenTrabajoContactologiaDetalles', CollectionType::class, array(
                        'entry_type'   => OrdenTrabajoContactologiaDetalleType::class,
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
                            'class' => 'table ordenTrabajoContactologiaDetalle-collection',
                        ],
                    ))
            ->add('finalOjoIzquierdoDnp',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'DNP Ojo Izquierdo'))
            ->add('finalOjoDerechoDnp',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'DNP Ojo Derecho'))
            ->add('finalCercaOjoIzquierdoEsfera',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Esfera Ojo Izquierdo'))
            ->add('finalCercaOjoDerechoEsfera',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Esfera Ojo Derecho'))
            ->add('finalCercaOjoIzquierdoCilindro',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Cilindro Ojo Izquiqerdo'))
            ->add('finalCercaOjoDerechoCilindro',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Cilindro Ojo Derecho'))
            ->add('finalCercaOjoIzquierdoEje',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Eje Ojo Izquierdo'))
            ->add('finalCercaOjoDerechoEje',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Eje Ojo Derecho'))
            ->add('finalLejosOjoIzquierdoEsfera',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Esfera Ojo Izquierdo'))
            ->add('finalLejosOjoDerechoEsfera',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Esfera Ojo Derecho'))
            ->add('finalLejosOjoIzquierdoCilindro',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Cilindro Ojo Izquierdo'))
            ->add('finalLejosOjoDerechoCilindro',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Cilindro Ojo Derecho'))
            ->add('finalLejosOjoIzquierdoEje',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Eje Ojo Izquierdo'))
            ->add('finalLejosOjoDerechoEje',FloatType::class, array(
                'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    'step' => 0.25,
                ),
                'label' => 'Eje Ojo Derecho'))
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
//            ->add('otrosTrabajos', null, array('label' => 'Otros Trabajos','required' => false,))

        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrdenTrabajoContactologia'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'OrdenTrabajoContactologiaType';
    }


}
