<?php

namespace Foutraz\Outlook\Dto;

use DateTimeImmutable;
use DateTimeZone;

class CalendarEvent
{
    public function __construct(
        public string $id,
        public ?string $subject,
        public ?string $bodyPreview,
        public ?string $location,
        public ?DateTimeImmutable $start,
        public ?DateTimeImmutable $end,
        public bool $allDay,
        public ?string $webLink,
        public ?string $organizer,
        public ?DateTimeImmutable $lastModified,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            $data['subject'] ?? null,
            $data['bodyPreview'] ?? null,
            $data['location']['displayName'] ?? null,
            self::parseGraphDateTime($data['start'] ?? null),
            self::parseGraphDateTime($data['end'] ?? null),
            (bool) ($data['isAllDay'] ?? false),
            $data['webLink'] ?? null,
            $data['organizer']['emailAddress']['name'] ?? $data['organizer']['emailAddress']['address'] ?? null,
            isset($data['lastModifiedDateTime']) ? new DateTimeImmutable($data['lastModifiedDateTime']) : null,
        );
    }

    /**
     * Builds an immutable date from a Microsoft Graph dateTimeTimeZone payload.
     *
     * @param  array<string, mixed>|null  $value
     */
    private static function parseGraphDateTime(?array $value): ?DateTimeImmutable
    {
        if ($value === null || ! isset($value['dateTime'])) {
            return null;
        }

        $timeZone = isset($value['timeZone']) ? new DateTimeZone((string) $value['timeZone']) : null;

        return new DateTimeImmutable((string) $value['dateTime'], $timeZone);
    }
}
