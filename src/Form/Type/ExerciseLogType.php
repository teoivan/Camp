<?php

namespace App\Form\Type;

use App\Entity\ExerciseLog;
use App\Entity\Type;
use App\Entity\Workout;
use App\Repository\WorkoutRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExerciseLogType extends AbstractType
{
    private $security;
    private $workoutRepository;

    public function __construct(Security $security, WorkoutRepository $workoutRepository)
    {
        $this->security = $security;
        $this->workoutRepository = $workoutRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void{

        $us=$this->security->getUser();
        $user=$us->getId();
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
            ])
            ->add('workout', EntityType::class, [
                'class' => Workout::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a workout',
                'query_builder' => function (WorkoutRepository $repo) use ($user) {
                    return $repo->createQueryBuilder('w')
                        ->andWhere('w.user = :user')
                        ->setParameter('user', $user)
                        ->orderBy('w.name', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExerciseLog::class,
        ]);
    }
}