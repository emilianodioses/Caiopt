<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LibroCajaDetalleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tipos = array(
            'Ingreso a Caja' => 'Ingreso a Caja',
            'Egreso de Caja' => 'Egreso de Caja');

        $builder->add('tipo',ChoiceType::class,array(
                      'label'=>'Tipo',
                      'required' => true,
                      'choices' => $tipos,
                          'choices_as_values' => true))
                ->add('descripcion', TextType::class, array(
                      'label' => 'Descripción',
                      'required' => true,
                    ))
                ->add('importe',FloatType::class, array(
                      'attr' => array(
                          'step' => 0.01, 'class' => 'importe'
                      ),
                      'required' => true,
                      'label' => 'Importe'))
                ->add('pagoTipo', EntityType::class, array(
                    'label' => 'Tipo de Pago',
                    'class' => 'AppBundle:PagoTipo',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           },
                    'attr' => [
                            'class' => 'pagoTipo',
                        ],
                    ))
                ->add('movimientoCategoria', EntityType::class, array(
                    'label' => 'Categoría',
                    'class' => 'AppBundle:MovimientoCategoria',
                    'required' => true,
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->addOrderBy('l.nombre', 'ASC')
                                   ;
                           },
                    'attr' => [
                            'class' => 'movimientoCategoria',
                        ],
                    ));
                /*
                ->add('activo')
                ->add('createdBy')
                ->add('createdAt')
                ->add('updatedBy')
                ->add('updatedAt')
                ->add('libroCaja')
                ->add('clientePago')
                ->add('proveedorPago');
                */
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\LibroCajaDetalle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_librocajadetalle';
    }


}
