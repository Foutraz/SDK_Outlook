<?php

namespace Foutraz\Outlook\Dto;

class Calendar
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $isDefaultCalendar,
        public bool $canEdit,
        public ?string $owner,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (string) ($data['id'] ?? ''),
            (string) ($data['name'] ?? ''),
            (bool) ($data['isDefaultCalendar'] ?? false),
            (bool) ($data['canEdit'] ?? false),
            $data['owner']['name'] ?? $data['owner']['address'] ?? null,
        );
    }
}
