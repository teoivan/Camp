<?php

namespace App\Form\Type;

use App\Entity\ExerciseLog;
use App\Entity\Type;
use App\Entity\Workout;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExerciseLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void{

        $builder
            ->add('nr_reps', NumberType::class)
            ->add('duration', TimeType::class)
            ->add('workout', EntityType::class, [
                'class' => Workout::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a workout',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExerciseLog::class,
        ]);
    }
}