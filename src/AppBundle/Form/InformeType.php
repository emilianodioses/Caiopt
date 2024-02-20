<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class InformeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        if (is_array($options['sucursales'])) {
            $choices = $this->transformSucursalesToChoices($options['sucursales']);
        }

        $builder
            ->add('sucursal', ChoiceType::class, [
                'choices' => $choices,
                'choice_label' => 'nombre',
                'multiple' => true,
                'required' => false,
                'expanded' => true,
            ]);
    }

    private function transformSucursalesToChoices($sucursales)
    {
        $choices = [];
        foreach ($sucursales as $sucursal) {
            if ($sucursal instanceof Sucursal) {
                $choices[$sucursal->getId()] = $sucursal->getNombre();
            } else {
                // Si $sucursal no es un objeto Sucursal, se asume que es el nombre de la sucursal
                $choices[] = $sucursal; // Utilizamos $sucursal como valor en lugar de índice
            }
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'sucursales' => [], // Definir sucursales como una opción predeterminada
        ]);
    }
}



