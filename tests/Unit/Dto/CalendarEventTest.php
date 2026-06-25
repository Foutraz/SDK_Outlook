<?php

namespace Foutraz\Outlook\Tests\Unit\Dto;

use DateTimeImmutable;
use Foutraz\Outlook\Dto\CalendarEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CalendarEventTest extends TestCase
{
    #[Test]
    public function it_maps_a_timed_event(): void
    {
        $event = CalendarEvent::fromArray([
            'id' => 'AAMkevent1',
            'subject' => 'Sprint planning',
            'bodyPreview' => 'Discuss the sprint',
            'isAllDay' => false,
            'webLink' => 'https://outlook.office365.com/event/1',
            'lastModifiedDateTime' => '2024-03-01T10:15:00Z',
            'location' => ['displayName' => 'Room 42'],
            'start' => ['dateTime' => '2024-03-02T09:00:00.0000000', 'timeZone' => 'UTC'],
            'end' => ['dateTime' => '2024-03-02T10:00:00.0000000', 'timeZone' => 'UTC'],
            'organizer' => ['emailAddress' => ['name' => 'Jane Doe', 'address' => 'jane@example.com']],
        ]);

        $this->assertSame('AAMkevent1', $event->id);
        $this->assertSame('Sprint planning', $event->subject);
        $this->assertSame('Discuss the sprint', $event->bodyPreview);
        $this->assertSame('Room 42', $event->location);
        $this->assertFalse($event->allDay);
        $this->assertSame('https://outlook.office365.com/event/1', $event->webLink);
        $this->assertSame('Jane Doe', $event->organizer);
        $this->assertInstanceOf(DateTimeImmutable::class, $event->start);
        $this->assertSame('2024-03-02T09:00:00+00:00', $event->start?->format('c'));
        $this->assertSame('2024-03-02T10:00:00+00:00', $event->end?->format('c'));
        $this->assertSame('2024-03-01T10:15:00+00:00', $event->lastModified?->format('c'));
    }

    #[Test]
    public function it_maps_an_all_day_event(): void
    {
        $event = CalendarEvent::fromArray([
            'id' => 'AAMkevent2',
            'subject' => 'Company holiday',
            'isAllDay' => true,
            'start' => ['dateTime' => '2024-12-25T00:00:00.0000000', 'timeZone' => 'UTC'],
            'end' => ['dateTime' => '2024-12-26T00:00:00.0000000', 'timeZone' => 'UTC'],
        ]);

        $this->assertTrue($event->allDay);
        $this->assertSame('Company holiday', $event->subject);
        $this->assertSame('2024-12-25T00:00:00+00:00', $event->start?->format('c'));
        $this->assertNull($event->location);
        $this->assertNull($event->organizer);
    }

    #[Test]
    public function it_defaults_missing_dates_to_null(): void
    {
        $event = CalendarEvent::fromArray([
            'id' => 'AAMkevent3',
        ]);

        $this->assertNull($event->start);
        $this->assertNull($event->end);
        $this->assertNull($event->subject);
        $this->assertNull($event->lastModified);
        $this->assertFalse($event->allDay);
    }
}
