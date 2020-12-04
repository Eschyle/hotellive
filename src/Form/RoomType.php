<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Room;
use App\Form\DataTransformer\FileTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    private $fileTransformer;

    public function __construct(FileTransformer $fileTransformer){
        $this->fileTransformer = $fileTransformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', MoneyType::class, [
                'grouping' => true,
                ])
            ->add('number', IntegerType::class, [])
            ->add('name', TextType::class, [])
            ->add('image', FileType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Choisissez une image'],
            ])
            ->add('options', EntityType::class, [
                'class' => Option::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
        ;
        $builder->get('image')
            ->addModelTransformer($this->fileTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
