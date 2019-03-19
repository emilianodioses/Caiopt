<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class OrdenTrabajoDetalleType extends AbstractType
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

        $builder->add('ordenTrabajo',HiddenType::class,array('label'=>'Orden Trabajo'))
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
                ->add('fechaEntrega',DateType::class,array(
                    'label'=>'Fecha Entrega',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('estado',ChoiceType::class,array(
                        'label'=>'Estado',
                        'choices' => $estados,
                            'choices_as_values' => true)) ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrdenTrabajoDetalle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'OrdenTrabajoDetalleType';
    }


}
