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
                    'attr' => array('size' => 3, 'placeholder' => 'Cantidad', 'class' => 'cantidad')))
                ->add('bonificacion',null,array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Bonificación', 'class' => 'bonificacion')))
                ->add('precioUnitario',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Precio Unitario', 'class' => 'precioUnitario'),
                    'divisor' => 1,
                    'currency' => 'ARS',))
                ->add('total',MoneyType::class, array(
                    'label' => false,
                    'divisor' => 1,
                    'attr' => array('size' => 3, 'placeholder' => 'Total', 'class' => 'total'),
                    'currency' => 'ARS'))
                ->add('totalNoGravado',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Total no Gravado', 'class' => 'totalNoGravado'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('totalNeto',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Total Neto', 'class' => 'totalNeto'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('importeIvaExento',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Importe Iva Exento', 'class' => 'importeIvaExento'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('importeIva',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Importe Iva', 'class' => 'importeIva'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('importeTributos',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Importe Tributos', 'class' => 'importeTributos'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('observaciones',HiddenType::class,array('label'=>'Observaciones'))
                ->add('precioCosto',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Precio Costo', 'class' => 'precioCosto'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('ganancia',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Ganancia', 'class' => 'ganancia'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
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
