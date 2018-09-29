<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ComprobanteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tipo',ChoiceType::class,array(
                        'label'=>'Tipo',
                        'choices' => array(
                            'Factura A' => 'Factura A', 
                            'Factura B' => 'Factura B',
                            'Nota Credito A' => 'Nota Credito A',
                            'Nota Credito B' => 'Nota Credito B',
                            'Nota Debito A' => 'Nota Debito A',
                            'Nota Debito B' => 'Nota Debito B'),
                            'choices_as_values' => true)) 
                ->add('fecha',DateType::class,array(
                    'label'=>'Fecha',
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd-MM-yyyy',
                    'required' => true,
                    'attr' => ['class' => 'js-datepicker']))
                ->add('puntoVenta', null, array('label' => 'Punto de Venta'))
                ->add('numero', null, array('label' => 'N de Comprobante'))
                ->add('totalBonificacion',null, array('label' => 'Bonificación'))
                ->add('total',MoneyType::class, array(
                    'divisor' => 1,
                    'currency' => 'ARS',
                    'label' => 'Total'))
                ->add('totalNoGravado',MoneyType::class, array(
                    'divisor' => 1,
                    'currency' => 'ARS',
                    'label' => 'Total no Gravado',))
                ->add('totalNeto',MoneyType::class, array(
                    'divisor' => 1,
                    'currency' => 'ARS',
                    'label' => 'Total Neto',))
                ->add('importeIvaExento',MoneyType::class, array(
                    'divisor' => 1,
                    'currency' => 'ARS',
                    'label' => 'Importe Exento',))
                ->add('importeIva',MoneyType::class, array(
                    'divisor' => 1,
                    'currency' => 'ARS',
                    'label' => 'Importe Iva'))
                ->add('importeTributos',MoneyType::class, array(
                    'divisor' => 1,
                    'currency' => 'ARS',
                    'label' => 'Importe Tributos'))
                ->add('observaciones',HiddenType::class,array('label'=>'Observaciones'))
                ->add('obraSocialId',HiddenType::class,array('label'=>'Obra Social'))
                ->add('obraSocialPlanId',HiddenType::class,array('label'=>'Plan Obra Social'))
                ->add('totalCosto',MoneyType::class, array(
                    'divisor' => 1,
                    'currency' => 'ARS',
                    'label' => 'Total Costo'))
                ->add('totalGanancia',MoneyType::class, array(
                    'divisor' => 1,
                    'currency' => 'ARS',
                    'label' => 'Total Ganancia'))
                ->add('proveedor', EntityType::class, array(
                    'label' => 'Proveedor',
                    'class' => 'AppBundle:Proveedor',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('cliente', EntityType::class, array(
                    'label' => 'Cliente',
                    'class' => 'AppBundle:Cliente',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('articulos', CollectionType::class, array(
                        'entry_type' => ComprobanteDetalleType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                        'label' => 'Articulos'))
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Comprobante'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_comprobante';
    }


}
