<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ProveedorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre', TextType::class, array(
            'label' => 'Nombre',
            'attr' => array('style' => 'text-transform: uppercase')
        ));

        $builder->add('documentoTipo', EntityType::class, array(
            'label' => 'Tipo Documento',
            'class' => 'AppBundle:AfipDocumentoTipo',
            'required' => true,
            'choice_label' => 'descripcion',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                       return $er->createQueryBuilder('ic')
                           ->where('ic.activo = 1')
                           ->orderBy('ic.descripcion', 'ASC')
                           ;
                   }
        ));

        $builder->add('documentoNumero', null, array(
            'label' => 'Número Documento',
        ));

        $builder->add('ivaCondicion', EntityType::class, array(
            'label' => 'Condición de IVA',
            'class' => 'AppBundle:AfipIvaCondicion',
            'required' => true,
            'choice_label' => 'descripcion',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                       return $er->createQueryBuilder('ic')
                           ->where('ic.activo = 1')
                           ->orderBy('ic.descripcion', 'ASC')
                           ;
                   }
        ));

        $builder->add('localidad', EntityType::class, array(
            'label' => 'Localidad',
            'class' => 'AppBundle:Localidad',
            'required' => true,
            'choice_label' => 'nombre',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                       return $er->createQueryBuilder('l')
                           ->where('l.activo = 1')
                           ->orderBy('l.nombre', 'ASC')
                           ;
                   }
        ));

        $builder->add('direccion', TextType::class, array(
            'label' => 'Dirección',
            'required' => false,
            'empty_data' => '',
            'attr' => array('style' => 'text-transform: uppercase')
        ));

        $builder->add('telefono', null, array(
            'label' => 'Teléfono',
            'required' => false,
            'empty_data' => '',
        ));

        $builder->add('email', TextType::class, array(
            'label' => 'Email',
            'required' => false,
            'empty_data' => '',
        ));

        $builder->add('contacto', null, array(
            'label' => 'Contacto',
            'required' => false,
            'empty_data' => '',
            'attr' => array('style' => 'text-transform: uppercase')
        ));

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Proveedor'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_proveedor';
    }


}
