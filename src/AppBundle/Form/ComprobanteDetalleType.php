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
                    'attr' => array('size' => 3, 'placeholder' => 'Cantidad')))
                ->add('bonificacion',null,array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Bonificación')))
                ->add('precioUnitario',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Precio Unitario'),
                    'divisor' => 1,
                    'currency' => 'ARS',))
                ->add('total',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Total'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('totalNoGravado',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Total no Gravado'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('totalNeto',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Total Neto'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('importeIvaExento',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Importe Iva Exento'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('importeIva',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Importe Iva'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('importeTributos',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Importe Tributos'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('observaciones',HiddenType::class,array('label'=>'Observaciones'))
                ->add('precioCosto',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Precio Costo'),
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('ganancia',MoneyType::class, array(
                    'label' => false,
                    'attr' => array('size' => 3, 'placeholder' => 'Ganancia'),
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
        return 'appbundle_comprobantedetalle';
    }


}
