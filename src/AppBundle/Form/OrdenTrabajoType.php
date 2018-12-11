<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OrdenTrabajoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('cliente', EntityType::class, array(
                    'label' => 'Cliente',
                    'class' => 'AppBundle:Cliente',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('referencia')
                ->add('ojoDerechoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'Eje Ojo Derecho'))
                ->add('ojoDerechoCilindro',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'Cilindro Ojo Derecho'))
                ->add('ojoDerechoEsfera',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,                        
                    ),
                    'label' => 'Esfera Ojo Derecho'))
                ->add('ojoDerechoAdicc',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'Adicc Ojo Derecho'))
                ->add('ojoDerechoDnp',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'DNP Ojo Derecho'))
                ->add('ojoDerechoAlt',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'ALT Ojo Derecho'))
                ->add('ojoIzquierdoEje',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'Eje Ojo Izquierdo'))
                ->add('ojoIzquierdoCilindro',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'Cilindro Ojo Izquierdo'))
                ->add('ojoIzquierdoEsfera',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'Esfera Ojo Izquierdo'))
                ->add('ojoIzquierdoAdicc',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'Adicc Ojo Izquierdo'))
                ->add('ojoIzquierdoDnp',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'DNP Ojo Izquierdo'))
                ->add('ojoIzquierdoAlt',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'ALT Ojo Izquierdo'))
                ->add('dip',IntegerType::class, array(
                    'attr' => array(
                    'required' => true,
                    ),
                    'label' => 'DIP'))
                ->add('cristales', null, array('label' => 'Cristales',))
                ->add('montura', null, array('label' => 'Montura',))
                ->add('observaciones',TextareaType::class,array(
                    'label'=>'Observaciones',
                    'required' => false,
                    'attr' => array('rows' => '3')
                ))
                ->add('otrosTrabajos', null, array('label' => 'Otros Trabajos',))
                ->add('taller', EntityType::class, array(
                    'label' => 'Taller',
                    'class' => 'AppBundle:Taller',
                    'required' => true,
                    'choice_label' => 'nombre',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er) {
                               return $er->createQueryBuilder('l')
                                   ->where('l.activo = 1')
                                   ->orderBy('l.nombre', 'ASC')
                                   ;
                           }
                ))
                ->add('diasEstimados',IntegerType::class, array(
                    'attr' => array(
                        'readonly' => true,
                    ),
                    'label' => 'Tiempo EST'))
                ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OrdenTrabajo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_ordentrabajo';
    }


}
