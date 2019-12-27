<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ComprobanteDetalleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        $builder->add('articulo', EntityType::class, array(
                    'label' => 'Articulo',
                    'class' => 'AppBundle:Articulo',
                    'required' => true,
                    'choice_label' => 'descripcion',
                    'placeholder' => ' ',
                    'attr' => [
                            'class' => 'articulo',
                        ],
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.descripcion', 'ASC')
                                   ;
                           }
                ));
                /*
                ->add('porcentajeIva',FloatType::class, array(
                    'label' => false,
                    'attr' => array('readonly' => true, 'size' => 3, 'placeholder' => 'Porcentaje Iva', 'class' => 'porcentajeIva', 'step' => 0.01),
                    ))
                */

        $porcentajeIva = array(
            '0.00' => '0.00',
            '10.50' => '10.50',
            '21.00' => '21.00'
        );

        //if (false) {
        if ($options['attr']['tipo'] == 'Compra') {
            //Falla ----NO FUNCIONA EL SELECT2 CUANDO SE AGREGA DINAMICAMENTE
            $builder->add('articulo', Select2EntityType::class, array(
                    'label' => 'Articulo',
                    'class' => 'AppBundle\Entity\Articulo',
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
                    'multiple' => false,
                    'allow_add' => array(
                        'enabled' => true,
                        'new_tag_text' => '',
                        'new_tag_prefix' => '__',
                        'tag_separators'=> '[",", ""]'
                        ),
                ));
        }
        else {
            $builder->add('articulo', Select2EntityType::class, array(
                    'label' => 'Articulo',
                    'class' => 'AppBundle\Entity\Articulo',
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
                    'multiple' => false,
                    ));
        }
        $builder->add('comprobante',HiddenType::class,array('label'=>'Comprobante'))
                ->add('cantidad',IntegerType::class,array(
                    'required' => true,
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Cantidad', 'class' => 'cantidad')))
                ->add('precioUnitario',FloatType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Precio Unitario', 'class' => 'precioUnitario', 'step' => 0.01),
                    ))
                ->add('porcentajeBonificacion',FloatType::class,array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Bonificación', 'class' => 'porcentajeBonificacion', 'step' => 0.01),
                    ))
                ->add('importeBonificacion',HiddenType::class,array(
                    'label'=>'Importe Bonificación',
                    'attr' => array('class' => 'importeBonificacion')))
                ->add('porcentajeIva',ChoiceType::class,array(
                        'label'=> false,
                        'choices' => $porcentajeIva,
                        'choices_as_values' => true,
                        'attr' => array('class' => 'porcentajeIva',),
                        'required' => true,))
                ->add('importeIva',HiddenType::class,array(
                    'label'=>'Importe Iva',
                    'attr' => array('class' => 'importeIva')))
                ->add('precioCosto',FloatType::class, array(
                    'label' => false,
                    'attr' => array('readonly' => true, 'size' => 3, 'placeholder' => 'Precio Costo', 'class' => 'precioCosto', 'step' => 0.01),
                    ))
                ->add('precioVenta',FloatType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Precio Venta', 'class' => 'precioVenta', 'step' => 0.01),
                    ))
                ->add('importeGanancia',HiddenType::class,array(
                    'label'=>'Importe Ganancia',
                    'attr' => array('class' => 'importeGanancia', 'step' => 0.01)))
                ->add('porcentajeGanancia',FloatType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Ganancia %', 'class' => 'porcentajeGanancia')))
                ->add('totalNeto',FloatType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Total Neto', 'class' => 'totalNeto', 'step' => 0.01),
                    ))
                ->add('total',FloatType::class, array(
                    'label' => false,
                    'attr' => array('readonly' => true, 'size' => 3, 'placeholder' => 'Total', 'class' => 'total', 'step' => 0.01),
                    ))
                ->add('totalNoGravado',HiddenType::class,array('label'=>'Total no Gravado'))
                ->add('importeIvaExento',HiddenType::class,array('label'=>'Importe Iva Exento'))
                ->add('observaciones',HiddenType::class,array(
                    'label'=>'Observaciones',
                    'attr' => [
                        'class' => 'observaciones',
                    ],
                ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ComprobanteDetalle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ComprobanteDetalleType';
    }


}
