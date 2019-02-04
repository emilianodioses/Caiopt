<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\ChoicesListType;


class ArticuloType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $generos = array(
            'Masculino' => 'Masculino',
            'Femenino' => 'Femenino',
            'Niños' => 'Niños');

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
                ->add('codigo', null, array('label' => 'Codigo',))
                ->add('descripcion', null, array('label' => 'Descripcion',))
                ->add('precioCosto', null, array('label' => 'Precio Costo',))
                ->add('gananciaPorcentaje', null, array('label' => 'Ganancia %',))
                ->add('precioVenta', null, array('label' => 'Precio de Venta',))
                
                ->add('cantidad', null, array('label' => 'Cantidad',))
                ->add('cantidadMinima', null, array('label' => 'Cantidad Minima',))
                ->add('precioModifica', null, array('label' => 'Precio Modificable',))
                ->add('ordenTrabajo', null, array('label' => 'Orden de Trabajo',))
                ->add('genero',ChoiceType::class,array(
                        'label'=>'Genero',
                        'choices' => $generos,
                            'choices_as_values' => true))  
                ->add('material', null, array('label' => 'Material','required'=> false,))
                ->add('forma', null, array('label' => 'Forma','required'=> false,))
                ->add('estilo', null, array('label' => 'Estilo','required'=> false,))
                ->add('color_marco', null, array('label' => 'Color Marco','required'=> false,))
                ->add('color_cristal', null, array('label' => 'Color Cristal','required'=> false,))
                ->add('activo', null, array('label' => 'Activo',))
                ->add('categoriaId', EntityType::class, array(
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
                ->add('marcaId', EntityType::class, array(
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
