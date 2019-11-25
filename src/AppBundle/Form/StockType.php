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

class StockType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('articulo', Select2EntityType::class, array(
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
                ->add('cantidad',IntegerType::class,array(
                    'required' => true,
                    'label' => 'Cantidad',
                    'attr' => array('size' => 3, 'placeholder' => 'Cantidad', 'class' => 'cantidad')))
                ->add('sucursal', EntityType::class, array(
                    'label' => 'Sucursal Origen',
                    'class' => 'AppBundle:Sucursal',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                            return $er->createQueryBuilder('ic')
                                ->where('ic.activo = 1')
                                ->orderBy('ic.nombre', 'ASC')
                                ;
                        }
                    ))
                ->add('moverstock',IntegerType::class,array(
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Cantidad a mover',
                    'attr' => array('size' => 3, 'placeholder' => 'Cantidad', 'class' => 'mover')))
                ->add('cantidadMinima', null, array('label' => 'Cantidad Minima',))
                ->add('sucursaldestino', EntityType::class, array(
                    'label' => 'Sucursal Destino',
                    'class' => 'AppBundle:Sucursal',
                    'mapped' => false,
                    'required' => false,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                            return $er->createQueryBuilder('ic')
                                ->where('ic.activo = 1')
                                ->orderBy('ic.nombre', 'ASC')
                                ;
                        }
                    ))
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Stock'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'StockType';
    }


}
