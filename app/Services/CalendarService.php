<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class CalendarService
{
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
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent select_account');
    }

    /**
     * Create a calendar event for an authenticated user
     */
    public function createSkillSwapEvent(User $user, $title, $description, $startDateTime, $endDateTime = null): ?Event
    {
        try {
            // FORCE RECONSTRUCTION: Handle raw string vs array
            $tokenData = is_string($user->google_access_token) 
                ? json_decode($user->google_access_token, true) 
                : $user->google_access_token;

            // If decoding failed or it's not a proper token array, rebuild it
            if (!$tokenData || !isset($tokenData['access_token'])) {
                $tokenData = [
                    'access_token'  => is_array($tokenData) ? ($tokenData['access_token'] ?? '') : $user->google_access_token,
                    'refresh_token' => $user->google_refresh_token,
                    'token_type'    => 'Bearer',
                    'expires_in'    => 3600,
                    'created'       => time()
                ];
            }
            
            // Set the token
            $this->client->setAccessToken($tokenData);

            // Check if token is expired and refresh if necessary
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $newAccessToken = $this->client->getAccessToken();
                
                // Store the refreshed token back to the database as JSON
                $user->update([
                    'google_access_token' => json_encode($newAccessToken),
                ]);
            }

            $this->calendarService = new Calendar($this->client);

            // If no end time specified, make it 1 hour after start time
            if (!$endDateTime) {
                $startDt = new \DateTime($startDateTime);
                $endDt = clone $startDt;
                $endDateTime = $endDt->modify('+1 hour')->format('Y-m-d H:i:s');
            }

            $event = new Event();
            $event->setSummary($title);
            $event->setDescription($description);
            $event->setStart(new EventDateTime([
                'dateTime' => (new \DateTime($startDateTime))->format(\DateTime::RFC3339),
                'timeZone' => config('app.timezone', 'UTC'),
            ]));
            $event->setEnd(new EventDateTime([
                'dateTime' => (new \DateTime($endDateTime))->format(\DateTime::RFC3339),
                'timeZone' => config('app.timezone', 'UTC'),
            ]));

            // Create the event on the primary calendar
            return $this->calendarService->events->insert('primary', $event);

        } catch (Exception $e) {
            Log::error('Calendar Event Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }
}