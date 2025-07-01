<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\ChoicesListType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class ArticuloType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('iva', EntityType::class, array(
                    'label' => 'IVA',
                    'class' => 'AppBundle:AfipAlicuota',
                    'required' => true,
                    'choice_label' => 'descripcion',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('ic')
                                   ->where('ic.activo = 1')
                                   ->orderBy('ic.descripcion', 'ASC')
                                   ;
                           }
                ))
                ->add('codigo', null, array(
                    'label' => 'Codigo',
                    'attr' => array('style' => 'text-transform: uppercase')
                ))
                ->add('descripcion', null, array(
                    'label' => 'Descripcion',
                    'attr' => array('style' => 'text-transform: uppercase')
                ))
                ->add('precioCosto', null, array('label' => 'Precio Costo',))
                ->add('gananciaPorcentaje', null, array('label' => 'Ganancia %',))
                ->add('precioVenta', null, array('label' => 'Precio de Venta','attr' => array('readonly' => true )))
                //->add('cantidadMinima', null, array('label' => 'Cantidad Minima',))
                ->add('precioModifica', HiddenType::class, array('label' => 'Precio Modificable',))
                ->add('ordenTrabajo', HiddenType::class, array('label' => 'Orden de Trabajo',))
                ->add('forma', HiddenType::class, array(
                    'label' => 'Forma',
                    'required'=> false,
                    'empty_data' => '',
                    'attr' => array('style' => 'text-transform: uppercase')
                ))
                ->add('marco', EntityType::class, array(
                    'label' => 'Marco',
                    'class' => 'AppBundle:ArticuloMarco',
                    'required' => true,
                    'choice_label' => 'descripcion',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                            ->where('l.activo = 1')
                            ->orderBy('l.descripcion', 'ASC')
                            ;
                    }
                ))
                ->add('colorMarco', null, array(
                    'label' => 'Color Marco',
                    'required'=> false,
                    'empty_data' => '',
                    'attr' => array('style' => 'text-transform: uppercase')
                ))
                ->add('colorCristal', null, array(
                    'label' => 'Color Cristal',
                    'required'=> false,
                    'empty_data' => '',
                    'attr' => array('style' => 'text-transform: uppercase')
                ))
                ->add('ancho', null, array('label' => 'Ancho',))
                ->add('alto', null, array('label' => 'Alto',))
                ->add('mayor_distancia', null, array('label' => 'Distancia Mayor',))
                ->add('puente', null, array('label' => 'Puente',))
                ->add('tipoAro', EntityType::class, array(
                    'label' => 'Tipo Aro',
                    'class' => 'AppBundle:ArticuloEstilo',
                    'required' => true,
                    'choice_label' => 'descripcion',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                            ->where('l.activo = 1')
                            ->orderBy('l.descripcion', 'ASC')
                            ;
                    }
                ))
                ->add('activo', null, array('label' => 'Activo',))
                    ->add('categoria', EntityType::class, array(
                        'label' => 'Categoria',
                        'class' => 'AppBundle:ArticuloCategoria',
                        'required' => true,
                        'choice_label' => 'descripcion',
                        'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                                   return $er->createQueryBuilder('l')
                                       ->where('l.activo = 1')
                                       ->orderBy('l.descripcion', 'ASC')
                                       ;
                               }
                    ))
                ->add('marca', EntityType::class, array(
                    'label' => 'Marca',
                    'class' => 'AppBundle:ArticuloMarca',
                    'required' => true,
                    'choice_label' => 'descripcion',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.descripcion', 'ASC')
                                   ;
                           }
                ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Articulo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_articulo';
    }


}
