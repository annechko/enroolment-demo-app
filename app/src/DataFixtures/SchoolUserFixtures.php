<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Core\Common\UuidGenerator;
use App\Core\School\Entity\School\Email;
use App\Core\School\Entity\School\InvitationToken;
use App\Core\School\Entity\School\Name;
use App\Core\School\Entity\School\School;
use App\Core\School\Entity\School\SchoolId;
use App\Core\School\Entity\School\StaffMember;
use App\Core\School\Entity\School\StaffMemberId;
use App\Core\School\Entity\School\StaffMemberName;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class SchoolUserFixtures extends AbstractUserGroupFixtures
{
    private const PASSWORD = 'school';
    private const EMAIL = 'school@school.school';
    private const NAME = 'default school';
    private const ADMIN_NAME = 'school admin name';
    private const ADMIN_SURNAME = 'school admin surname';

    public function __construct(
        private readonly PasswordHasherFactoryInterface $hasherFactory,
        private readonly UuidGenerator $uuidGenerator,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $school = School::register(
            new SchoolId($this->uuidGenerator->generate()),
            new Name(self::NAME),
            new StaffMemberId($this->uuidGenerator->generate()),
            new StaffMemberName(self::ADMIN_NAME, self::ADMIN_SURNAME),
            new Email(self::EMAIL)
        );
        $school->confirmRegister(
            new InvitationToken(
                $this->uuidGenerator->generate(),
                new \DateTimeImmutable()
            )
        );
        $school->getAdmin()->confirmAccount(
            $this->hasherFactory->getPasswordHasher(StaffMember::class)
                ->hash(self::PASSWORD)
        );

        $manager->persist($school);
        $manager->flush();
    }
}
