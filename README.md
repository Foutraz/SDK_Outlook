# foutraz/outlook

A framework-agnostic PHP SDK for the [Microsoft Graph](https://learn.microsoft.com/en-us/graph/api/resources/calendar) Outlook calendar API, with first-class Laravel integration (ServiceProvider + Facade). Built on Guzzle, it returns typed DTOs, maps HTTP errors to named exceptions, and handles the Microsoft identity platform OAuth token exchange/refresh.

## Requirements

- PHP `^8.4`
- `guzzlehttp/guzzle` `^7.0`
- `illuminate/support` `^11.0|^12.0` (Laravel integration only)

## Installation

```bash
composer require foutraz/outlook
```

## Environment variables

| Variable | Description | Default |
| --- | --- | --- |
| `OUTLOOK_CLIENT_ID` / `AZURE_CLIENT_ID` | Azure AD application (client) id | — |
| `OUTLOOK_CLIENT_SECRET` / `AZURE_CLIENT_SECRET` | Azure AD application client secret | — |
| `OUTLOOK_REDIRECT_URI` / `AZURE_REDIRECT_URI` | OAuth callback URL registered in Azure | — |
| `OUTLOOK_TENANT` / `AZURE_TENANT` | Directory tenant id | `common` |
| `OUTLOOK_BASE_URL` | Graph API base endpoint | `https://graph.microsoft.com/v1.0` |
| `OUTLOOK_TOKEN` | Default user access token | — |

In Laravel, the `OutlookServiceProvider` is auto-discovered and binds `OutlookManager` as a singleton resolvable via the `Outlook` facade.

## Building a manager

### Laravel

```php
use Foutraz\Outlook\OutlookManager;

$outlook = app(OutlookManager::class);
```

### Standalone

```php
use Foutraz\Outlook\OutlookManager;

$outlook = new OutlookManager(
    endpoint: 'https://graph.microsoft.com/v1.0',
    apiToken: $accessToken,
    clientId: $clientId,
    clientSecret: $clientSecret,
    redirectUri: $redirectUri,
    tenant: 'common',
);
```

## OAuth flow

### 1. Redirect the user to the Microsoft identity platform

```php
$url = $outlook->auth()->authorizeUrl(); // default scope ['Calendars.Read', 'offline_access', 'User.Read']
$url = $outlook->auth()->authorizeUrl(['Calendars.ReadWrite', 'offline_access']);
```

### 2. Exchange the callback code for tokens

```php
use Foutraz\Outlook\Dto\TokenResponse;

$token = $outlook->auth()->exchangeToken($_GET['code']); // TokenResponse

$token->accessToken;
$token->refreshToken; // ?string
$token->expiresAt;    // unix timestamp (now + expires_in)
$token->scope;        // ?string
```

### 3. Refresh the token

```php
$token = $outlook->auth()->refreshToken($storedRefreshToken);

$outlook->setToken($token->accessToken);
// Persist $token->refreshToken and $token->expiresAt.
```

## Calendars

```php
$calendars = $outlook->calendars()->list(); // array<Calendar>

$calendars[0]->name;
$calendars[0]->isDefaultCalendar; // bool
$calendars[0]->canEdit;           // bool
$calendars[0]->owner;             // ?string
```

## Events

```php
// Events for the signed-in user, with optional OData query params.
$events = $outlook->events()->list([
    '$top' => 25,
    '$orderby' => 'start/dateTime',
    '$filter' => "categories/any(c:c eq 'Work')",
]);

// Events within a date range (expands recurring series).
$events = $outlook->events()->calendarView('2024-03-01T00:00:00Z', '2024-03-31T23:59:59Z');

// A single event.
$event = $outlook->events()->find('AAMkAGI2...');

$event->subject;       // ?string
$event->bodyPreview;   // ?string
$event->location;      // ?string (location.displayName)
$event->start;         // DateTimeImmutable|null
$event->end;           // DateTimeImmutable|null
$event->allDay;        // bool (isAllDay)
$event->webLink;       // ?string
$event->organizer;     // ?string
$event->lastModified;  // DateTimeImmutable|null
```

## DTOs

All DTOs live under `Foutraz\Outlook\Dto` and expose a static `fromArray(array): self` factory mapping Microsoft Graph payloads to typed properties: `Calendar`, `CalendarEvent`, and `TokenResponse`.

## Error handling

HTTP errors are mapped to named exceptions under `Foutraz\Outlook\Exceptions`:

| Status | Exception |
| --- | --- |
| 401 | `Unauthorized` |
| 404 | `ResourceNotFound` |
| 422 | `InvalidData` |
| 429 | `TooManyRequestsException` |
| other 4xx/5xx | `ActionFailed` |

## Testing

```bash
composer test
```
