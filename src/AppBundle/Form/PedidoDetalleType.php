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


class PedidoDetalleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $porcentajeIva = array(
            '0.00' => '0.00',
            '10.50' => '10.50',
            '21.00' => '21.00'
        );

        $builder->add('pedido',HiddenType::class,array('label'=>'Pedido'))
                ->add('articulo', Select2EntityType::class, array(
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
                    )) 
                ->add('cantidad',IntegerType::class,array(
                    'required' => true,
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Cantidad', 'class' => 'cantidad')))
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PedidoDetalle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'PedidoDetalleType';
    }


}
