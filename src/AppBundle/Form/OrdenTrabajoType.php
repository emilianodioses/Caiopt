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
                ->add('comprobante', EntityType::class, array(
                    'label' => 'Comprobante',
                    'class' => 'AppBundle:Comprobante',
                    'required' => true,
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->andWhere('l.movimiento = ?1')
                                   ->setParameter(1, 'Venta')
                                   ->orderBy('l.id', 'ASC')
                                   ;
                           }
                ))
                ->add('ojoDerechoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Derecho'))
                ->add('ojoDerechoCilindro',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Cilindro Ojo Derecho'))
                ->add('ojoDerechoEsfera',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,                        
                    'empty_data' => 0,
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('ojoDerechoAdicc',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Adicc Ojo Derecho'))
                ->add('ojoDerechoDnp',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'DNP Ojo Derecho'))
                ->add('ojoDerechoAlt',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'ALT Ojo Derecho'))
                ->add('ojoIzquierdoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Eje Ojo Izquierdo'))
                ->add('ojoIzquierdoCilindro',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Cilindro Ojo Izquierdo'))
                ->add('ojoIzquierdoEsfera',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                ->add('ojoIzquierdoAdicc',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'Adicc Ojo Izquierdo'))
                ->add('ojoIzquierdoDnp',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'DNP Ojo Izquierdo'))
                ->add('ojoIzquierdoAlt',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'ALT Ojo Izquierdo'))
                ->add('dip',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    'empty_data' => 0,
                    ),
                    'label' => 'DIP'))
                ->add('cristales', null, array('label' => 'Cristales',))
                ->add('montura', null, array('label' => 'Montura',))
                ->add('observaciones',TextareaType::class,array(
                    'label'=>'Observaciones',
                    'required' => false,
                    'empty_data' => ''  ,
                    'attr' => array('rows' => '3')
                ))
                ->add('otrosTrabajos', null, array('label' => 'Otros Trabajos','required' => false,))
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
                ->add('estado',ChoiceType::class,array(
                        'label'=>'Estado',
                        'choices' => $estados,
                            'choices_as_values' => true)) 
                ->add('diasEstimados',IntegerType::class, array(
                    'attr' => array(
                    'required' => false,
                    ),
                    'label' => 'Tiempo EST'))
                ->add('fecha',DateType::class,array(
                    'label'=>'Fecha',
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
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ;
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
        return 'appbundle_ordentrabajo';
    }


}
