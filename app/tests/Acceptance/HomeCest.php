<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\AcceptanceTester;

class HomeCest
{
    public function testHome_asUnauthorized_seeTitle(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Enrollment Demo Application');
    }
}
