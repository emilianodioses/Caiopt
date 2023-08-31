<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Form\ChoicesListType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ParametroType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigo', TextType::class, array(
            'label' => 'Codigo',
            'required' => false,
            'empty_data' => ''
                ))
                ->add('descripcion', TextType::class, array(
                    'label' => 'Descripcion',
                    'required' => false,
                    'empty_data' => ''
                ))
                ->add('valorTexto', TextType::class, array(
                    'label' => 'ValorTexto',
                    'required' => false,
                    'empty_data' => ''
                ))
                ->add('valorNro', null, array(
                    'label' => 'valorNro',
                    'required' => false,
                    'empty_data' => '',
                ))
                ->add('valorImporte',FloatType::class, array(
                    'attr' => array(
                        'readonly' => false, 'step' => 0.01),
                    'label' => 'ValorImporte'));
    }
    /**
 * {@inheritdoc}
 */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Parametro'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_parametro';
    }


}
