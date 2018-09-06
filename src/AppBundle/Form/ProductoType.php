<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ProductoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('modelo', TextType::class, array('label' => 'Modelo'))
                ->add('descripcion',TextareaType::class,array(
                    'label'=>'Descripción', 
                    'attr' => array('maxlength'=> '255', 'cols' => '5', 'rows' => '5')))
                ->add('precio', MoneyType::class, array(
                    'divisor' => 1,
                    'currency' => 'ARS'))
                ->add('categoria', EntityType::class, array(
                    'label' => 'Categoria',
                    'class' => 'AppBundle:ProductoCategoria',
                    'required' => true,
                    'choice_label' => 'descripcion',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                       return $er->createQueryBuilder('pc')
                           ->where('pc.activo = 1')
                           ->orderBy('pc.descripcion', 'ASC');
                        }
                    ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Producto'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_producto';
    }


}
