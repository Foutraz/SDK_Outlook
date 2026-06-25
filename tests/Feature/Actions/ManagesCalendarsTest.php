<?php

namespace Foutraz\Outlook\Tests\Feature\Actions;

use Foutraz\Outlook\Dto\Calendar;
use Foutraz\Outlook\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ManagesCalendarsTest extends TestCase
{
    #[Test]
    public function it_lists_calendars_as_dtos(): void
    {
        $manager = $this->managerWithResponses([
            $this->jsonResponse(200, [
                'value' => [
                    ['id' => 'c1', 'name' => 'Calendar', 'isDefaultCalendar' => true, 'canEdit' => true],
                    ['id' => 'c2', 'name' => 'Birthdays', 'isDefaultCalendar' => false, 'canEdit' => false],
                ],
            ]),
        ]);

        $calendars = $manager->calendars()->list();

        $this->assertCount(2, $calendars);
        $this->assertContainsOnlyInstancesOf(Calendar::class, $calendars);
        $this->assertSame('Calendar', $calendars[0]->name);
        $this->assertStringContainsString('me/calendars', $this->lastRequestUri());
    }

    #[Test]
    public function it_returns_an_empty_list_when_no_value_key(): void
    {
        $manager = $this->managerWithResponses([$this->jsonResponse(200, [])]);

        $calendars = $manager->calendars()->list();

        $this->assertSame([], $calendars);
    }
}
