<?php

namespace Foutraz\Outlook\Tests\Feature\Actions;

use Foutraz\Outlook\Dto\CalendarEvent;
use Foutraz\Outlook\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ManagesEventsTest extends TestCase
{
    #[Test]
    public function it_lists_events_as_dtos(): void
    {
        $manager = $this->managerWithResponses([
            $this->jsonResponse(200, [
                'value' => [
                    ['id' => 'e1', 'subject' => 'A', 'isAllDay' => false],
                    ['id' => 'e2', 'subject' => 'B', 'isAllDay' => true],
                ],
            ]),
        ]);

        $events = $manager->events()->list();

        $this->assertCount(2, $events);
        $this->assertContainsOnlyInstancesOf(CalendarEvent::class, $events);
        $this->assertSame('A', $events[0]->subject);
        $this->assertTrue($events[1]->allDay);
    }

    #[Test]
    public function it_passes_query_params_when_listing(): void
    {
        $manager = $this->managerWithResponses([$this->jsonResponse(200, ['value' => []])]);

        $manager->events()->list(['$top' => 10, '$orderby' => 'start/dateTime', '$filter' => "isAllDay eq true"]);

        $uri = $this->lastRequestUri();
        $this->assertStringContainsString('top=10', $uri);
        $this->assertStringContainsString('orderby', $uri);
        $this->assertStringContainsString('filter', $uri);
    }

    #[Test]
    public function it_fetches_a_calendar_view_with_a_date_range(): void
    {
        $manager = $this->managerWithResponses([
            $this->jsonResponse(200, [
                'value' => [
                    ['id' => 'e1', 'subject' => 'Standup', 'isAllDay' => false],
                ],
            ]),
        ]);

        $events = $manager->events()->calendarView('2024-03-01T00:00:00Z', '2024-03-31T23:59:59Z');

        $this->assertCount(1, $events);
        $uri = $this->lastRequestUri();
        $this->assertStringContainsString('me/calendarView', $uri);
        $this->assertStringContainsString('startDateTime', $uri);
        $this->assertStringContainsString('endDateTime', $uri);
    }

    #[Test]
    public function it_finds_a_single_event(): void
    {
        $manager = $this->managerWithResponses([
            $this->jsonResponse(200, ['id' => 'e42', 'subject' => 'Solo', 'isAllDay' => false]),
        ]);

        $event = $manager->events()->find('e42');

        $this->assertSame('e42', $event->id);
        $this->assertSame('Solo', $event->subject);
        $this->assertStringContainsString('me/events/e42', $this->lastRequestUri());
    }
}
