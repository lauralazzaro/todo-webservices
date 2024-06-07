<?php

namespace App\Form;

use App\Entity\Task;
use App\Validator\DeadlineInFuture;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content', TextareaType::class)
            ->add('deadline', DateType::class, [
                'widget' => 'choice',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'dd-MMM-yyyy',
                'constraints' => [
                    new DeadlineInFuture(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
