<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MedicoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre', TextType::class, array(
                    'label' => 'Nombre',
                    'attr' => array('style' => 'text-transform: uppercase')
                ))
                ->add('documentoTipo', EntityType::class, array(
                    'label' => 'Tipo Documento',
                    'class' => 'AppBundle:AfipDocumentoTipo',
                    'required' => false,
                    'choice_label' => 'descripcion',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('ic')
                                   ->where('ic.activo = 1')
                                   ->orderBy('ic.descripcion', 'ASC')
                                   ;
                           }
                ))
                ->add('matricula', null, array(
                    'label' => 'Matricula',
                    'required' => true))
                ->add('documentoNumero', null, array(
                    'label' => 'Número Documento',
                    'required' => false,
                    'empty_data' => '',))
                ->add('localidad', EntityType::class, array(
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
                ))
                ->add('direccion', TextType::class, array(
                    'label' => 'Dirección',
                    'required' => false,
                    'empty_data' => '',
                    'attr' => array('style' => 'text-transform: uppercase')
                ))
                ->add('telefono', null, array(
                    'label' => 'Teléfono',
                    'required' => false,
                    'empty_data' => '',))
                ->add('email', TextType::class, array(
                    'label' => 'Email',
                    'required' => false,
                    'empty_data' => '',))
                ->add('contacto', null, array(
                    'label' => 'Contacto',
                    'required' => false,
                    'empty_data' => '',
                    'attr' => array('style' => 'text-transform: uppercase')))
                ->add('porcentaje', null, array(
                    'label' => 'Porcentaje',
                    'required' => false,
                    'empty_data' => ''));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Medico'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_medico';
    }


}
