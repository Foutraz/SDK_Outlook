<?php

namespace Foutraz\Outlook\Actions;

use Foutraz\Outlook\Dto\Calendar;
use Foutraz\Outlook\Exceptions\ActionFailed;
use Foutraz\Outlook\Exceptions\InvalidData;
use Foutraz\Outlook\Exceptions\ResourceNotFound;
use Foutraz\Outlook\Exceptions\TooManyRequestsException;
use Foutraz\Outlook\Exceptions\Unauthorized;
use Foutraz\Outlook\OutlookManager;
use GuzzleHttp\Exception\GuzzleException;

class ManagesCalendars extends OutlookManager
{
    /**
     * @return array<int, Calendar>
     *
     * @throws ActionFailed
     * @throws GuzzleException
     * @throws InvalidData
     * @throws ResourceNotFound
     * @throws TooManyRequestsException
     * @throws Unauthorized
     */
    public function list(): array
    {
        $response = $this->get('me/calendars');

        return array_map(static fn (array $calendar): Calendar => Calendar::fromArray($calendar), $response['value'] ?? []);
    }
}
