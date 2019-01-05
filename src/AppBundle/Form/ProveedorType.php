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
            'label' => 'Documento Número',
        ));

        $builder->add('ivaCondicion', EntityType::class, array(
            'label' => 'Condición IVA',
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
        ));

        $builder->add('telefono', null, array(
            'label' => 'Teléfono',
        ));

        $builder->add('email', TextType::class, array(
            'label' => 'Email',
        ));

        $builder->add('contacto', null, array(
            'label' => 'Contacto',
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
