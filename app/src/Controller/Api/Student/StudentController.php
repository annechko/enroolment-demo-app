<?php

declare(strict_types=1);

namespace App\Controller\Api\Student;

use App\Controller\Api\AbstractApiController;
use App\Domain\Core\UuidPattern;
use App\Domain\School\Common\RoleEnum;
use App\ReadModel\Student\Filter;
use App\ReadModel\Student\SchoolFetcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/student')]
class StudentController extends AbstractApiController
{
    private const MAX_ITEMS = 20;

    #[Route('/application/schools', name: 'api_student_application_school_list')]
    public function applicationSchoolList(
        Request $request,
        SchoolFetcher $fetcher
    ): Response {
        $this->denyAccessUnlessGranted(RoleEnum::STUDENT_USER->value);

        $filter = new Filter\SchoolFilter();

        $form = $this->createForm(Filter\SchoolForm::class, $filter);
        $form->handleRequest($request);
        $schools = $fetcher->fetchSchools(
            $filter,
            self::MAX_ITEMS,
        );

        $result = [];
        foreach ($schools as $item) {
            $result[] = [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        }

        return new JsonResponse($result);
    }

    #[Route('/application/schools/{schoolId}/courses',
        name: 'api_student_application_course_list',
        requirements: ['schoolId' => UuidPattern::PATTERN_WITH_TEMPLATE],
    )]
    public function applicationSchoolCoursesList(
        Request $request,
        string $schoolId,
        SchoolFetcher $fetcher
    ): Response {
        $this->denyAccessUnlessGranted(RoleEnum::STUDENT_USER->value);

        $filter = new Filter\CourseFilter($schoolId);

        $form = $this->createForm(Filter\CourseForm::class, $filter);
        $form->handleRequest($request);
        $courses = $fetcher->fetchSchoolCourses(
            $filter,
            self::MAX_ITEMS,
        );

        $result = [];
        foreach ($courses as $item) {
            $result[] = [
                'id' => $item['id'],
                'name' => $item['name'],
            ];
        }

        return new JsonResponse($result);
    }

    #[Route('/application/schools/{schoolId}/courses/{courseId}/intakes',
        name: 'api_student_application_intake_list',
        requirements: [
            'schoolId' => UuidPattern::PATTERN_WITH_TEMPLATE,
            'courseId' => UuidPattern::PATTERN_WITH_TEMPLATE,
        ],
    )]
    public function applicationCourseIntakesList(
        Request $request,
        string $schoolId,
        string $courseId,
        SchoolFetcher $fetcher
    ): Response {
        $this->denyAccessUnlessGranted(RoleEnum::STUDENT_USER->value);

        $filter = new Filter\IntakeFilter($schoolId, $courseId);

        $form = $this->createForm(Filter\IntakeForm::class, $filter);
        $form->handleRequest($request);
        $intakes = $fetcher->fetchCourseIntakes(
            $filter,
        );

        $result = [];
        foreach ($intakes as $item) {
            $result[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'start' => $item['start_date'],
                'end' => $item['end_date'],
            ];
        }

        return new JsonResponse($result);
    }
}
