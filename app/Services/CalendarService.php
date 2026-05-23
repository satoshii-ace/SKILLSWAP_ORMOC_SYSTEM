<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Carbon\Carbon;

class CalendarService
{
    protected Client $client;
    protected Calendar $calendarService;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setHttpClient(new GuzzleClient(['timeout' => 10]));
        $this->client->setApplicationName('SkillSwap');
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent select_account');
    }

    /**
     * Create a calendar event for an authenticated user and invite attendees with custom start/end markers
     */
    public function createSkillSwapEvent(User $user, $title, $description, $startDateTime, $endDateTime = null, array $attendees = []): ?Event
    {
        try {
            $tokenData = is_string($user->google_access_token) 
                ? json_decode($user->google_access_token, true) 
                : $user->google_access_token;

            if (!$tokenData || !isset($tokenData['access_token'])) {
                $tokenData = [
                    'access_token'  => is_array($tokenData) ? ($tokenData['access_token'] ?? '') : $user->google_access_token,
                    'refresh_token' => $user->google_refresh_token,
                    'token_type'    => 'Bearer',
                    'expires_in'    => 3600,
                    'created'       => time()
                ];
            }
            
            $this->client->setAccessToken($tokenData);

            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $newAccessToken = $this->client->getAccessToken();
                
                $user->update([
                    'google_access_token' => json_encode($newAccessToken),
                ]);
            }

            $this->calendarService = new Calendar($this->client);

            // Establish the explicit timestamp references
            $start = new \DateTime($startDateTime);
            $end = $endDateTime ? new \DateTime($endDateTime) : (clone $start)->modify('+1 hour');

            $event = new Event();
            $event->setSummary($title);
            $event->setDescription($description);
            
            $targetTimezone = 'Asia/Manila';

$start = Carbon::parse($startDateTime, $targetTimezone);
$end = $endDateTime 
    ? Carbon::parse($endDateTime, $targetTimezone) 
    : $start->copy()->addHour();

$event = new Event();
$event->setSummary($title);
$event->setDescription($description);

$event->setStart(new EventDateTime([
    'dateTime' => $start->toRfc3339String(),
    'timeZone' => $targetTimezone,
]));

$event->setEnd(new EventDateTime([
    'dateTime' => $end->toRfc3339String(),
    'timeZone' => $targetTimezone,
]));

            if (!empty($attendees)) {
                $googleAttendees = [];
                foreach ($attendees as $email) {
                    $googleAttendees[] = ['email' => $email];
                }
                $event->setAttendees($googleAttendees);
            }

            Log::info('Event Payload: ' . json_encode($event));

            return $this->calendarService->events->insert('primary', $event, ['sendUpdates' => 'all']);

        } catch (Exception $e) {
            Log::error('Calendar Event Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function hasValidToken(User $user): bool
{
    if (!$user->google_access_token) {
        Log::info('Sync Check: No token found for user ' . $user->id);
        return false;
    }
    
    $tokenData = json_decode($user->google_access_token, true);
    
    if (!$tokenData || !isset($tokenData['access_token'])) {
        Log::info('Sync Check: Token is invalid JSON or missing access_token for user ' . $user->id);
        return false;
    }

    // New check: Is the token actually expired right now?
    $client = new \Google\Client();
    $client->setAccessToken($tokenData);
    
    if ($client->isAccessTokenExpired()) {
        Log::info('Sync Check: Token expired for user ' . $user->id);
        return false;
    }

    return true;
}
}