<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PedidoType extends AbstractType
{
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('proveedor', Select2EntityType::class, array(
                    'label' => 'Proveedor',
                    'class' => 'AppBundle:Proveedor',
                    'remote_route' => 'proveedor_find_select2',
                    'placeholder' => 'Seleccione un proveedor',
                    'required' => true,
                    'attr' => [
                            'class' => 'proveedor',
                        ],
                    'primary_key' => 'id',
                    'text_property' => 'nombre',
                    'minimum_input_length' => 2,
                    'page_limit' => 10,
                    'allow_clear' => false,
                    'delay' => 250,
                    'cache' => true,
                    'cache_timeout' => 60000, // if 'cache' is true
                    'language' => 'es',
                    ))
                ->add('numero',HiddenType::class,array('label'=>'N de Pedido'))
                ->add('fecha',DateType::class,array(
                    'label'=>'Fecha',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']))
                ->add('observaciones',TextareaType::class,array(
                    'label'=>'Observaciones',
                    'required' => false,
                    'attr' => array('rows' => '20')
                ))
                ->add('pedidoDetalles', CollectionType::class, array(
                        'entry_type'   => PedidoDetalleType::class,
                        'entry_options' => [
                            'attr' => [
                                'class' => 'item', // we want to use 'tr.item' as collection elements' selector
                            ],
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype'    => true,
                        'required'     => true,
                        'by_reference' => true,
                        'delete_empty' => true,
                        'attr' => [
                            'class' => 'table pedidoDetalle-collection',
                        ],
                    )
                );
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Pedido'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'PedidoType';
    }


}
