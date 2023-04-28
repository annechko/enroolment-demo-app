<?php

declare(strict_types=1);

namespace App\Domain\School\UseCase\School\Course\Add;

use App\ReadModel\School\CampusFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public const NAME = 'course';

    public function __construct(private readonly CampusFetcher $campusFetcher)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('campuses', Type\ChoiceType::class, [
                'multiple' => true,
                'choice_loader' => new CallbackChoiceLoader(function () {
                    return array_flip($this->campusFetcher->getCampusesIdToName());
                }),
            ]) // todo remove copy paste
            ->add('startDates', Type\CollectionType::class, [
                'allow_add' => true,
                'allow_extra_fields' => true,
                'delete_empty' => true,
                'entry_type' => Type\DateType::class,
                'entry_options' => [
                    'input' => 'datetime_immutable',
                    'widget' => 'single_text',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Command::class,
                'csrf_protection' => false,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return self::NAME;
    }
}
