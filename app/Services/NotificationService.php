<?php

namespace App\Services;

use App\Models\ConditionEvent;
use App\Models\Installation;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function notifyTechnician(ConditionEvent $event, string $message): bool
    {
        $technician = User::find($event->technician_id);

        if (! $technician) {
            return false;
        }

        return $this->sendExpoPushNotification($technician, $message, [
            'event_type' => $this->resolveEventType($event),
            'installation_id' => $event->installation_id,
            'recipient_role' => 'technician',
        ]);
    }

    public function notifyClient(ConditionEvent $event, string $message): bool
    {
        $installation = Installation::find($event->installation_id);

        if (! $installation) {
            return false;
        }

        $client = User::find($installation->client_id);

        if (! $client) {
            return false;
        }

        return $this->sendExpoPushNotification($client, $message, [
            'event_type' => $this->resolveEventType($event),
            'installation_id' => $event->installation_id,
            'recipient_role' => 'client',
        ]);
    }

    protected function sendExpoPushNotification(User $user, string $message, array $data = []): bool
    {
        if (! $user->expo_push_token) {
            logger()->warning('Push notification skipped: missing Expo token', [
                'user_id' => $user->id,
                'message' => $message,
            ]);

            return false;
        }

        $response = Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $user->expo_push_token,
            'title' => 'Solar Maintenance Alert',
            'body' => $message,
            'sound' => 'default',
            'data' => $data,
        ]);

        if ($response->failed()) {
            logger()->error('Expo push notification failed', [
                'user_id' => $user->id,
                'status' => $response->status(),
                'response' => $response->json() ?? $response->body(),
            ]);

            return false;
        }

        logger()->info('Expo push notification sent', [
            'user_id' => $user->id,
            'message' => $message,
        ]);

        return true;
    }

    protected function resolveEventType(ConditionEvent $event): string
    {
        return is_object($event->event_type)
            ? $event->event_type->value
            : (string) $event->event_type;
    }
}