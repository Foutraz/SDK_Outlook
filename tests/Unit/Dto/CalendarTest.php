<?php

namespace Foutraz\Outlook\Tests\Unit\Dto;

use Foutraz\Outlook\Dto\Calendar;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CalendarTest extends TestCase
{
    #[Test]
    public function it_maps_a_full_payload(): void
    {
        $calendar = Calendar::fromArray([
            'id' => 'AAMkAGI2',
            'name' => 'Calendar',
            'isDefaultCalendar' => true,
            'canEdit' => true,
            'owner' => ['name' => 'Jane Doe', 'address' => 'jane@example.com'],
        ]);

        $this->assertSame('AAMkAGI2', $calendar->id);
        $this->assertSame('Calendar', $calendar->name);
        $this->assertTrue($calendar->isDefaultCalendar);
        $this->assertTrue($calendar->canEdit);
        $this->assertSame('Jane Doe', $calendar->owner);
    }

    #[Test]
    public function it_defaults_flags_and_owner(): void
    {
        $calendar = Calendar::fromArray([
            'id' => 'AAMkAGI3',
            'name' => 'Birthdays',
        ]);

        $this->assertFalse($calendar->isDefaultCalendar);
        $this->assertFalse($calendar->canEdit);
        $this->assertNull($calendar->owner);
    }

    #[Test]
    public function it_falls_back_to_owner_address(): void
    {
        $calendar = Calendar::fromArray([
            'id' => 'AAMkAGI4',
            'name' => 'Team',
            'owner' => ['address' => 'team@example.com'],
        ]);

        $this->assertSame('team@example.com', $calendar->owner);
    }
}
