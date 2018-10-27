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


        $builder->add('codigo', null, array('label' => 'Codigo',))
                ->add('descripcion', null, array('label' => 'Descripcion',))
                ->add('precioCosto', null, array('label' => 'Precio Costo',))
                ->add('gananciaPorcentaje', null, array('label' => 'Ganancia %',))
                ->add('precioVenta', null, array('label' => 'Precio de Venta',))
                ->add('iva', null, array('label' => 'IVA'))
                ->add('cantidad', null, array('label' => 'Cantidad',))
                ->add('cantidadMinima', null, array('label' => 'Cantidad Minima',))
                ->add('precioModifica', null, array('label' => 'Precio Modificable',))
                ->add('genero',ChoiceType::class,array(
                        'label'=>'Genero',
                        'choices' => $generos,
                            'choices_as_values' => true))  
                ->add('material', null, array('label' => 'Materia',))
                ->add('forma', null, array('label' => 'Forma',))
                ->add('estilo', null, array('label' => 'Estilo',))
                ->add('color_marco', null, array('label' => 'Color Marco',))
                ->add('color_cristal', null, array('label' => 'Color Cristal',))
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
