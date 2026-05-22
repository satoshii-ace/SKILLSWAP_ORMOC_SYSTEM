<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use App\Models\User;
use Illuminate\Support\Facades\Log; // 1. Added the Log Facade import here!
use Exception;

class CalendarService
{
    // 2. Added type-hints here so VS Code knows what these are
    protected Client $client;
    protected Calendar $calendarService;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('SkillSwap');
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR);
    }

    /**
     * Create a calendar event for an authenticated user
     */
    public function createSkillSwapEvent(User $user, $title, $description, $startDateTime, $endDateTime = null): ?Event
    {
        try {
            // Decode and set the user's access token
            $accessToken = is_string($user->google_access_token) 
                ? json_decode($user->google_access_token, true)
                : $user->google_access_token;
            
            // Ensure access_token key exists for Google Client
            if (is_array($accessToken) && !isset($accessToken['access_token'])) {
                throw new Exception('Invalid Google access token format.');
            }
                
            $this->client->setAccessToken($accessToken);

            // Check if token needs refresh
            if ($this->client->isAccessTokenExpired()) {
                $this->client->refreshToken($user->google_refresh_token);
                // Store the refreshed token
                $newAccessToken = $this->client->getAccessToken();
                $user->update([
                    'google_access_token' => json_encode($newAccessToken),
                ]);
            }

            $this->calendarService = new Calendar($this->client);

            // If no end time specified, make it 1 hour after start time
            if (!$endDateTime) {
                $startDt = \DateTime::createFromFormat('Y-m-d H:i:s', $startDateTime);
                $endDateTime = $startDt->modify('+1 hour')->format('Y-m-d H:i:s');
            }

            $event = new Event();
            $event->setSummary($title);
            $event->setDescription($description);
            $event->setStart(new EventDateTime([
                'dateTime' => (new \DateTime($startDateTime))->format(\DateTime::RFC3339),
                'timeZone' => 'UTC',
            ]));
            $event->setEnd(new EventDateTime([
                'dateTime' => (new \DateTime($endDateTime))->format(\DateTime::RFC3339),
                'timeZone' => 'UTC',
            ]));

            // Create the event
            $createdEvent = $this->calendarService->events->insert('primary', $event);

            return $createdEvent;
        } catch (Exception $e) {
            // 3. Changed \Log::error to Log::error
            Log::error('Calendar Event Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }
}