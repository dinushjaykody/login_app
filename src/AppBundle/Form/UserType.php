<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label'     => 'Username',
                'required'  => true,
                'attr'      => array(
                    'placeholder' => 'Enter Username',
                    'class'       => 'form-control'
                )
            ))
            ->add('firstname', 'text', array(
                'label'     => 'First Name',
                'required'  => true,
                'attr'      => array(
                    'placeholder' => 'Enter First Name',
                    'class'       => 'form-control'
                )

            ))
            ->add('lastname','text', array(
                'label'     => 'Last Name',
                'required'  => true,
                'attr'      => array(
                    'placeholder' => 'Enter Last Name',
                    'class'       => 'form-control'
                )

            ))
            ->add('email', 'email', array(
                'label'     => 'Email',
                'required'  => true,
                'attr'      => array(
                    'placeholder' => 'Enter Email',
                    'class'       => 'form-control'
                )

            ))
            ->add('password', 'text', array(
                'label'     => 'Password',
                'required'  => true,
                'attr'      => array(
                    'placeholder' => 'Enter Password',
                    'class'       => 'form-control'
                )

            ))
            ->add('create','submit',array(
                'label' => 'SAVE',
                'attr'  => array(
                    'class' => 'btn btn-lg btn-primary'
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
}
