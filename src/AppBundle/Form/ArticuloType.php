<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticuloType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigo')->add('descripcion')->add('precioCosto')->add('gananciaPorcentaje')->add('precioVenta')->add('iva')->add('cantidad')->add('cantidadMinima')->add('precioModifica')->add('genero')->add('material')->add('forma')->add('estilo')->add('color_marco')->add('color_cristal')->add('activo')->add('createdBy')->add('createdAt')->add('updatedBy')->add('updatedAt')->add('categoriaId')->add('marcaId');
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
