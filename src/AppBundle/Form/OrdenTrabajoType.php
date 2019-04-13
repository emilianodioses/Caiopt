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

        $builder->add('cliente', EntityType::class, array(
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
                ->add('comprobante', EntityType::class, array(
                    'label' => 'Comprobante',
                    'class' => 'AppBundle:Comprobante',
                    'required' => false,
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->andWhere('l.movimiento = ?1')
                                   ->setParameter(1, 'Venta')
                                   ->orderBy('l.id', 'ASC')
                                   ;
                           }
                ))
                ->add('medico', null, array('label' => 'Medico','required' => false,))
                ->add('fechaReceta',DateType::class,array(
                    'label'=>'Fecha Receta',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
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
                    'label' => 'Taller',
                    'class' => 'AppBundle:Taller',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('fechaTallerPedido',DateType::class,array(
                    'label'=>'Fecha Pedido',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('fechaTallerEntrega',DateType::class,array(
                    'label'=>'Fecha Entrega',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => false,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('armado', null, array('label' => 'Armado','required' => false,))
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
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Derecho'))
                ->add('lejosOjoIzquierdoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Izquierdo'))
                ->add('lejosOjoDerechoCilindro',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Cilindro Ojo Derecho'))
                ->add('lejosOjoIzquierdoCilindro',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Cilindro Ojo Izquierdo'))
                ->add('lejosOjoDerechoEsfera',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('lejosOjoIzquierdoEsfera',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                ->add('cercaOjoDerechoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Derecho'))
                ->add('cercaOjoIzquierdoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Izquierdo'))
                ->add('cercaOjoDerechoCilindro',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Cilindro Ojo Derecho'))
                ->add('cercaOjoIzquierdoCilindro',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Cilindro Ojo Izquierdo'))
                ->add('cercaOjoDerechoEsfera',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('cercaOjoIzquierdoEsfera',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                ->add('ojoDerechoDnp',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'DNP OD'))
                ->add('ojoIzquierdoDnp',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'DNP OI'))
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
                        'required'     => false,
                        'by_reference' => true,
                        'delete_empty' => true,
                        'attr' => [
                            'class' => 'table ordenTrabajoDetalle-collection',
                        ],
                    )
                );
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
