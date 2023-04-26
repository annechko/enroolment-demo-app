<?php

declare(strict_types=1);

namespace App\Domain\School\UseCase\School\Course\Add;

use App\Domain\Core\UuidPattern;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, minMessage: 'Course name is too short. It should have {{ limit }} characters or more.')]
    public $name;
    public $description;

    #[Assert\All([
        new Assert\NotBlank,
        new Assert\Regex(pattern: UuidPattern::PATTERN_REG_EXP),
    ])]
    public $campuses;
}
