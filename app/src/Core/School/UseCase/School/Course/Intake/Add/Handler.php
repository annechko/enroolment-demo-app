<?php

declare(strict_types=1);

namespace App\Core\School\UseCase\School\Course\Intake\Add;

use App\Core\Common\Flusher;
use App\Core\Common\UuidGenerator;
use App\Core\School\Entity\Campus\CampusId;
use App\Core\School\Entity\Course\CourseId;
use App\Core\School\Entity\Course\Intake\IntakeId;
use App\Core\School\Repository\CampusRepository;
use App\Core\School\Repository\CourseRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class Handler
{
    public function __construct(
        private readonly CourseRepository $courseRepository,
        private readonly CampusRepository $campusRepository,
        private readonly Flusher $flusher,
        private readonly UuidGenerator $uuidGenerator,
    ) {
    }

    #[AsMessageHandler]
    public function handle(Command $command): void
    {
        $course = $this->courseRepository->get(new CourseId($command->courseId));
        $campus = null;
        if (!empty($command->campusId)) {
            $campus = $this->campusRepository->get(new CampusId($command->campusId));
        }
        $course->addIntake(
            new IntakeId($this->uuidGenerator->generate()),
            $command->startDate,
            $command->endDate,
            $command->name,
            is_numeric($command->classSize) ? (int) $command->classSize : null,
            $campus
        );

        $this->flusher->flush();
    }
}
