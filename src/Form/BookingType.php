<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Customer;
use App\Entity\Role;
use App\Entity\Room;
use App\Form\DataTransformer\DateTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class BookingType extends AbstractType
{
    private $dateTransformer;

    public function __construct(DateTransformer $dateTransformer){
        $this->dateTransformer = $dateTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', CustomerType::class)
            ->add('room', EntityType::class, [
                'label' => 'Chambre',
                'class' => Room::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'choice_attr' => function ($choice, $key, $value) {
                    return ['data-price' => $choice->getPrice()];
                }
            ])
//            ->add('startDate', DateType::class, [
//                'widget' => 'single_text'
//            ])
            ->add('startDate', TextType::class, [
                'label' => 'Date d\'arrivée',
                'attr' => ['disabled' => true]
            ])
            ->add('endDate', TextType::class, [
                'label' => 'Date de départ',
                'attr' => ['disabled' => true]
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => false,
            ])
//            ->add('customer', EntityType::class, [
//                'class' => Customer::class,
//                //par on a surchargé la méthode __toString() dans Customer Entity
////                'choice_label' => 'lastname',
//                //pas obligatoire car par défaut c'est l'id
////                'choice_value' => 'id'
//            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn-outline-success']
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $booking = $event->getData();
                $form = $event->getForm();

                dump($booking);
                $startDateString = $booking['startDate'];
                $date = \DateTime::createFromFormat('Y-m-d', $startDateString);
                $booking['startDate'] = $date;
            });
        $builder->get('startDate')
            ->addModelTransformer($this->dateTransformer);
        $builder->get('endDate')
            ->addModelTransformer($this->dateTransformer);
//            ->addModelTransformer(new CallbackTransformer(
//                function ($date) {
//                    if ($date){
////                        return $date->format('Y-m-d');
//                        return $date->format('d-m-Y');
//                    }
//                },
//                function ($dateString){
//                    if ($dateString) {
//                        return \DateTime::createFromFormat('Y-m-d', $dateString);
////                        return \DateTime::createFromFormat('d-m-Y', $dateString);
//                    }
//                }
//                )
//            );
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
