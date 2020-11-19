<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Customer;
use App\Entity\Role;
use App\Entity\Room;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('comment', TextareaType::class, [
                'required' => false
                ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                //par on a surchargé la méthode __toString() dans Customer Entity
//                'choice_label' => 'lastname',
                //pas obligatoire car par défaut c'est l'id
//                'choice_value' => 'id'
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
//                'choice_label' => 'number',
                'choice_value' => 'id'
            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn-outline-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
