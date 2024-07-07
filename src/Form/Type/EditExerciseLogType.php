<?php

namespace App\Form\Type;

use App\Entity\ExerciseLog;
use App\Entity\Workout;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditExerciseLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void{

        $builder
            ->add('nr_reps', NumberType::class)
            ->add('duration', TimeType::class, [
                'widget' => 'choice',
                'input' => 'datetime',
                'with_seconds' => true,
                'hours' => range(0, 0),
                'minutes' => range(0, 59),
                'seconds' => range(0, 59),
                'placeholder' => [
                    'hour' => 'hours',
                    'minute' => 'minutes',
                    'second' => 'seconds',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExerciseLog::class,
        ]);
    }
}