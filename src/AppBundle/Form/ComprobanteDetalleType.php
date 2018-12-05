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
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ComprobanteDetalleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
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
        $builder->add('articulo', Select2EntityType::class, array(
                    'label' => 'Articulo',
                    'class' => 'AppBundle:Articulo',
                    'required' => true,
                    'attr' => [
                            'class' => 'articulo',
                        ],
                    'remote_route' => 'articulo_find_all_json',


                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 2,
                    'page_limit' => 10,
                    'allow_clear' => true,
                    'delay' => 250,
                    'cache' => true,
                    'cache_timeout' => 60000, // if 'cache' is true
                    'placeholder' => 'Select a country',
                ));
                */

/*
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();

                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $articulo = $data->getArticulo();
                $precio = null === $articulo ? array() : $sport->getAvailablePositions();

                $form->add('position', EntityType::class, array(
                    'class' => 'AppBundle:Position',
                    'placeholder' => '',
                    'choices' => $positions,
                ));
            }
        );
*/
        $builder->add('cantidad',NumberType::class,array(
                    'label' => false,
                    'data' => 0,
                    'attr' => array('size' => 3, 'placeholder' => 'Cantidad', 'class' => 'cantidad')))
                ->add('bonificacion',null,array(
                    'label' => false,
                    'data' => 0,
                    'attr' => array('size' => 3, 'placeholder' => 'Bonificación', 'class' => 'bonificacion')))
                ->add('precioUnitario',NumberType::class, array(
                    'label' => false,
                    'data' => 0,
                    'attr' => array('size' => 3, 'placeholder' => 'Precio Costo', 'class' => 'precioUnitario'),
                    ))
                ->add('precioCosto',NumberType::class, array(
                    'label' => false,
                    'data' => 0,
                    'attr' => array('size' => 3, 'placeholder' => 'Precio Costo', 'class' => 'precioCosto'),
                    ))
                ->add('precioVenta',NumberType::class, array(
                    'label' => false,
                    'data' => 0,
                    'attr' => array('size' => 3, 'placeholder' => 'Precio Venta', 'class' => 'precioVenta'),
                    ))
                ->add('totalNeto',NumberType::class, array(
                    'label' => false,
                    'data' => 0,
                    'attr' => array('size' => 3, 'placeholder' => 'Total Neto', 'class' => 'totalNeto'),
                    ))
                ->add('importeIva',NumberType::class, array(
                    'label' => false,
                    'data' => 0,
                    'attr' => array(
                        'readonly' => true,
                    ),
                    'attr' => array('size' => 3, 'placeholder' => 'Importe Iva', 'class' => 'importeIva'),
                    ))
                ->add('total',NumberType::class, array(
                    'label' => false,
                    'data' => 0,
                    'attr' => array(
                        'readonly' => true,
                    ),
                    'attr' => array('size' => 3, 'placeholder' => 'Total', 'class' => 'total'),
                    ))
                ->add('ganancia',NumberType::class, array(
                    'label' => false,
                    'data' => 0,
                    'attr' => array('size' => 3, 'placeholder' => 'Ganancia', 'class' => 'ganancia')))
                ->add('totalNoGravado',HiddenType::class,array('label'=>'Total no Gravado'))
                ->add('importeIvaExento',HiddenType::class,array('label'=>'Importe Iva Exento'))
                ->add('importeGanancia',HiddenType::class,array('label'=>'Importe Ganancia'))
                ->add('observaciones',HiddenType::class,array('label'=>'Observaciones'))
                ->add('comprobante',HiddenType::class,array('label'=>'Comprobante'));
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
