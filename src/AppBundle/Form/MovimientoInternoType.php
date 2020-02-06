<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;


class MovimientoInternoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('monto',FloatType::class, array(
                    'label' => 'Importe',
                    'required' => true,
                    'attr' => array('size' => 3, 'placeholder' => 'Importe', 'class' => 'monto', 'step' => 0.01),
                    ))
                ->add('observaciones',TextareaType::class,array(
                    'label'=>'Observaciones',
                    'required' => false,
                    'attr' => array('rows' => '16')
                    ))
                ->add('sucursalOrigen', EntityType::class, array(
                    'label' => 'Sucursal Origen',
                    'class' => 'AppBundle:Sucursal',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.id', 'DESC')
                                   ;
                           }
                ))
                ->add('sucursalDestino', EntityType::class, array(
                    'label' => 'Sucursal Destino',
                    'class' => 'AppBundle:Sucursal',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.id', 'DESC')
                                   ;
                           }
                ))
                ->add('movimientoCategoria', EntityType::class, array(
                    'label' => 'Movimiento Categoria',
                    'class' => 'AppBundle:MovimientoCategoria',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.id', 'DESC')
                                   ;
                           }
                ))
                /*
                ->add('comprobante', EntityType::class, array(
                    'label' => 'Comprobante',
                    'class' => 'AppBundle:Comprobante',
                    'required' => false,
                    'choice_label' => 'id',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.id', 'DESC')
                                   ;
                           }
                ))
                */
                ->add('comprobante', Select2EntityType::class, array(
                    'label' => 'Comprobante',
                    'class' => 'AppBundle:Comprobante',
                    'remote_route' => 'comprobantecompra_find_select2',
                    'placeholder' => 'Seleccione un Comprobante',
                    'required' => false,
                    'attr' => [
                            'class' => 'comprobante',
                        ],
                    'primary_key' => 'id',
                    'text_property' => 'id',
                    'minimum_input_length' => 1,
                    'page_limit' => 10,
                    'allow_clear' => false,
                    'delay' => 250,
                    'cache' => true,
                    'cache_timeout' => 60000, // if 'cache' is true
                    'language' => 'es',
                    ))
                ->add('pagoTipo', EntityType::class, array(
                    'label' => 'Tipo Pago',
                    'class' => 'AppBundle:PagoTipo',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                            ->where('l.activo = 1')
                            ->orderBy('l.nombre', 'ASC')
                            ;
                    }
                ))
                ->add('fecha',DateType::class,array(
                    'label'=>'Fecha',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'html5' => true,
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off']));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MovimientoInterno'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_movimientointerno';
    }


}
