<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('usuario', TextType::class, array(
            'label' => 'Usuario',
        ));

        $builder->add('nombre', TextType::class, array(
            'label' => 'Nombre', 'required' => true
        ));

        $builder->add('apellido', TextType::class, array(
            'label' => 'Apellido', 'required' => true
        ));

        $builder->add('password', RepeatedType::class, array(
            'type' => PasswordType::class,
            'invalid_message' => 'Las contraseñas no coinciden.',
            'required' => true,
            'first_options'  => array('label' => 'Contraseña'),
            'second_options' => array('label' => 'Repita contraseña'),
        )); 

        $builder->add('email', TextType::class, array(
            'label' => 'E-Mail', 'required' => true
        ));
                
    }

    public function getBlockPrefix()
    {
        return 'appbundle_usuario';
    }


}
