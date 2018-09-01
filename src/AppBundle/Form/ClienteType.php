<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ClienteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre', TextType::class, array('label' => 'Nombre',))
                ->add('documentoTipo',ChoiceType::class,array(
                    'label' => 'Tipo Documento',
                    'choices_as_values' => true,
                    'choices' => array(
                        'CUIT/CUIL' => 'CUIT/CUIL',
                        'DNI' => 'DNI'
                        ), 
                ))
                ->add('documentoNumero', null, array('label' => 'Numero Documento',))
                ->add('ivaCondicion', EntityType::class, array(
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
                ->add('direccion', TextType::class, array('label' => 'Dirección',))
                ->add('telefono', null, array('label' => 'Teléfono',))
                ->add('email', TextType::class, array('label' => 'Email',))
                ->add('contacto', null, array('label' => 'Contacto',));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cliente'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cliente';
    }


}
