<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;


class PresupuestoDetalleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $formasPago = array(
            'Cuenta Corriente' => 'Cuenta Corriente',
            'Efectivo' => 'Efectivo');

        $builder->add('cantidad',IntegerType::class,array(
                    'required' => true,
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Cantidad', 'class' => 'cantidad')))
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
                ->add('parametro', Select2EntityType::class, array(
                    'label' => 'Parametro',
                    'class' => 'AppBundle:Parametro',
                    'remote_route' => 'parametro_find_select2',
                    'placeholder' => 'Seleccione un parametro',
                    'required' => true,
                    'attr' => [
                        'class' => 'parametro',
                    ],
                    'primary_key' => 'id',
                    'text_property' => 'valorTexto',
                    'minimum_input_length' => 0,
                    'page_limit' => 10,
                    'allow_clear' => false,
                    'delay' => 250,
                    'cache' => true,
                    'cache_timeout' => 60000, // if 'cache' is true
                    'language' => 'es',
                ))
                ->add('valorNro', IntegerType::class, array(
                    'label' => false,
                    'attr' => array('readonly' => true, 'placeholder' => '%', 'class' => 'valorNro'),
                ))
                ->add('porcentajeBonificacion',HiddenType::class,array(
                    'label'=>'% Bonificación',
                    'attr' => array('readonly' => true, 'size' => 3, 'placeholder' => '% Bonificacion', 'class' => 'porcentajeBonificacion', 'step' => 0.1),
                        ))
                /*
                ->add('formaPago',ChoiceType::class,array(
                    'label'=>'Forma de Pago',
                    'choices' => $formasPago,
                    'choices_as_values' => true))*/
                ->add('precioUnit',FloatType::class,array(
                    'label'=>'Precio Venta',
                    'attr' => array('readonly' => false, 'size' => 3, 'placeholder' => 'Precio Venta', 'class' => 'precioUnit', 'step' => 0.01),
                ))
                ->add('importeBonificacion',FloatType::class,array(
                    'label'=>'Importe Bonificación',
                    'attr' => array('readonly' => false, 'size' => 3, 'placeholder' => 'Bonificacion', 'class' => 'importeBonificacion', 'step' => 0.01),
                ))
                ->add('totalDetalle',FloatType::class, array(
                    'label' => false,
                    'attr' => array('readonly' => true, 'size' => 3, 'placeholder' => 'Total Detalle', 'class' => 'totalDetalle', 'step' => 0.01),
                ));


    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PresupuestoDetalle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'PresupuestoDetalleType';
    }


}
