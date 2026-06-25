<?php

namespace Foutraz\Outlook\Tests\Unit\Concerns;

use Foutraz\Outlook\Exceptions\ActionFailed;
use Foutraz\Outlook\Exceptions\InvalidData;
use Foutraz\Outlook\Exceptions\ResourceNotFound;
use Foutraz\Outlook\Exceptions\TooManyRequestsException;
use Foutraz\Outlook\Exceptions\Unauthorized;
use Foutraz\Outlook\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MakesHttpRequestsTest extends TestCase
{
    #[Test]
    public function it_maps_401_to_unauthorized(): void
    {
        $manager = $this->managerWithResponses([$this->jsonResponse(401, ['error' => ['code' => 'InvalidAuthenticationToken']])]);

        $this->expectException(Unauthorized::class);

        $manager->calendars()->list();
    }

    #[Test]
    public function it_maps_404_to_resource_not_found(): void
    {
        $manager = $this->managerWithResponses([$this->jsonResponse(404, [])]);

        $this->expectException(ResourceNotFound::class);

        $manager->events()->find('missing');
    }

    #[Test]
    public function it_maps_422_to_invalid_data(): void
    {
        $manager = $this->managerWithResponses([$this->jsonResponse(422, ['error' => ['code' => 'ErrorInvalidIdMalformed']])]);

        $this->expectException(InvalidData::class);

        $manager->calendars()->list();
    }

    #[Test]
    public function it_maps_400_to_action_failed(): void
    {
        $manager = $this->managerWithResponses([$this->jsonResponse(400, ['error' => ['code' => 'BadRequest']])]);

        $this->expectException(ActionFailed::class);

        $manager->calendars()->list();
    }

    #[Test]
    public function it_maps_429_to_too_many_requests(): void
    {
        $manager = $this->managerWithResponses([$this->jsonResponse(429, ['error' => ['code' => 'TooManyRequests']])]);

        $this->expectException(TooManyRequestsException::class);

        $manager->calendars()->list();
    }
}
