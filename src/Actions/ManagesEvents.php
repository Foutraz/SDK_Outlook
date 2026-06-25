<?php

namespace Foutraz\Outlook\Actions;

use Foutraz\Outlook\Dto\CalendarEvent;
use Foutraz\Outlook\Exceptions\ActionFailed;
use Foutraz\Outlook\Exceptions\InvalidData;
use Foutraz\Outlook\Exceptions\ResourceNotFound;
use Foutraz\Outlook\Exceptions\TooManyRequestsException;
use Foutraz\Outlook\Exceptions\Unauthorized;
use Foutraz\Outlook\OutlookManager;
use GuzzleHttp\Exception\GuzzleException;

class ManagesEvents extends OutlookManager
{
    /**
     * @param  array<string, mixed>  $params
     * @return array<int, CalendarEvent>
     *
     * @throws ActionFailed
     * @throws GuzzleException
     * @throws InvalidData
     * @throws ResourceNotFound
     * @throws TooManyRequestsException
     * @throws Unauthorized
     */
    public function list(array $params = []): array
    {
        $response = $this->get('me/events', $params);

        return array_map(static fn (array $event): CalendarEvent => CalendarEvent::fromArray($event), $response['value'] ?? []);
    }

    /**
     * @return array<int, CalendarEvent>
     *
     * @throws ActionFailed
     * @throws GuzzleException
     * @throws InvalidData
     * @throws ResourceNotFound
     * @throws TooManyRequestsException
     * @throws Unauthorized
     */
    public function calendarView(string $start, string $end): array
    {
        $response = $this->get('me/calendarView', [
            'startDateTime' => $start,
            'endDateTime' => $end,
        ]);

        return array_map(static fn (array $event): CalendarEvent => CalendarEvent::fromArray($event), $response['value'] ?? []);
    }

    /**
     * @throws ActionFailed
     * @throws GuzzleException
     * @throws InvalidData
     * @throws ResourceNotFound
     * @throws TooManyRequestsException
     * @throws Unauthorized
     */
    public function find(string $eventId): CalendarEvent
    {
        return CalendarEvent::fromArray($this->get("me/events/$eventId"));
    }
}
