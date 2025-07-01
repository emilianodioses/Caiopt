<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SucursalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre', TextType::class, array(
                    'label' => 'Nombre',
                    'required' => false,
                    'empty_data' => ''
                ))
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
                    'empty_data' => '',
                ))
                ->add('tecnicoOptico', null, array(
                    'label' => 'Técnico Óptico',
                    'required' => false,
                    'empty_data' => '',
                ))
                ->add('tecnicoOpticoMatricula', null, array(
                    'label' => 'Técnico Óptico Matrícula',
                    'required' => false,
                    'empty_data' => '',
                ))
                ->add('tecnicoContactologo', null, array(
                    'label' => 'Técnico Contactólogo',
                    'required' => false,
                    'empty_data' => '',
                ))
                ->add('tecnicoContactologoMatricula', null, array(
                    'label' => 'Técnico Contactólogo Matrícula',
                    'required' => false,
                    'empty_data' => '',
                ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Sucursal'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_sucursal';
    }


}
