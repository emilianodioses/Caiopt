<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class OrdenTrabajoDetalleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $estados = array(
            'Nuevo' => 'Nuevo',
            'Pendiente' => 'Pendiente',
            'Enviado' => 'Enviado',
            'Finalizado' => 'Finalizado');

        $builder->add('ordenTrabajo',HiddenType::class,array('label'=>'Orden Trabajo'))
                ->add('articulo', Select2EntityType::class, array(
                    'label' => 'Articulo',
                    'class' => 'AppBundle:Articulo',
                    'remote_route' => 'articulo_find_select2',
                    'placeholder' => 'Seleccione un artículo',
                    'required' => true,
                    'attr' => [
                            'class' => 'articulo',
                        ],
                    'primary_key' => 'id',
                    'text_property' => 'descripcion',
                    'minimum_input_length' => 2,
                    'page_limit' => 10,
                    'allow_clear' => false,
                    'delay' => 250,
                    'cache' => true,
                    'cache_timeout' => 60000, // if 'cache' is true
                    'language' => 'es',
                    )) 
                ->add('fechaEntrega',DateType::class,array(
                    'label'=>false,
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'placeholder' => 'Fecha Entrega', 'autocomplete' => 'off']))
                ->add('precioVenta',FloatType::class, array(
                    'label' => false,
                    'attr' => array('readonly' => false, 'size' => 3, 'placeholder' => 'Precio Venta', 'class' => 'precioVenta', 'step' => 0.01),
                    ))
                ->add('total',FloatType::class, array(
                    'label' => false,
                    'attr' => array('readonly' => true, 'size' => 3, 'placeholder' => 'Total', 'class' => 'total', 'step' => 0.01),
                    ))
                ->add('importeBonificacion',FloatType::class,array(
                    'label'=>'Importe Bonificación',
                    'attr' => array('readonly' => false, 'size' => 3, 'placeholder' => 'Obra Social/Bonificacion', 'class' => 'importeBonificacion', 'step' => 0.01),
                        ))
                ->add('tipoCristal',ChoiceType::class,array(
                    'label'=>false,
                    'choices' => $tipoCristal,
                    'choices_as_values' => true))        
                ->add('estado',ChoiceType::class,array(
                    'label'=>false,
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
