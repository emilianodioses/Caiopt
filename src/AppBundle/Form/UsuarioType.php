<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UsuarioType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('usuario', TextType::class, array(
            'label' => 'Usuario',
            'required' => true
        ));

        $builder->add('nombre', TextType::class, array(
            'label' => 'Nombre',
            'required' => true,
            'attr' => array('style' => 'text-transform: uppercase')
        ));

        $builder->add('apellido', TextType::class, array(
            'label' => 'Apellido', 
            'required' => true,
            'attr' => array('style' => 'text-transform: uppercase')
        ));

        $builder->add('password', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'Las contraseñas no coinciden.',
            'required' => true,
            'first_options'  => array('label' => 'Contraseña'),
            'second_options' => array('label' => 'Repita contraseña'),
        )); 

        $builder->add('rol', EntityType::class, array(
            'label' => 'Rol',
            'class' => 'AppBundle:Rol',
            'required' => true,
            'choice_label' => 'descripcion',
            'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                   return $er->createQueryBuilder('ic')
                       ->where('ic.activo = 1')
                       ->orderBy('ic.descripcion', 'ASC')
                       ;
               }
        ));

        $builder->add('email', TextType::class, array(
            'label' => 'E-Mail', 'required' => true
        ));
                
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Usuario'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_usuario';
    }


}
