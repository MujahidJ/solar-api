<?php

namespace App\Services;

use App\Enums\ConditionEventType;
use App\Models\ConditionEvent;
use App\Models\Reminder;

class EventActionService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(ConditionEvent $event): array
    {
        $eventType = $this->normalizeEventType($event);

        return match ($eventType) {
            ConditionEventType::LOW_VOLTAGE => $this->handleLowVoltage($event),
            ConditionEventType::BATTERY_FAILURE => $this->handleBatteryFailure($event),
            ConditionEventType::PANEL_FAULT => $this->handlePanelFault($event),
            ConditionEventType::INVERTER_ISSUE => $this->handleInverterIssue($event),
            ConditionEventType::OVERHEATING => $this->handleOverheating($event),
        };
    }

    protected function handleLowVoltage(ConditionEvent $event): array
    {
        $message = 'Low voltage detected. Immediate check required.';

        $reminder = $this->createConditionReminder($event, 'urgent', $message);

        $technicianNotified = $this->notificationService->notifyTechnician($event, $message);

        return [
            'reminder' => $reminder,
            'notifications' => [
                'technician_notified' => $technicianNotified,
            ],
        ];
    }

    protected function handleBatteryFailure(ConditionEvent $event): array
    {
        $technicianMessage = 'Battery failure detected. Urgent replacement required.';
        $clientMessage = 'Your solar system requires urgent maintenance.';

        $reminder = $this->createConditionReminder($event, 'critical', $technicianMessage);

        $technicianNotified = $this->notificationService->notifyTechnician($event, $technicianMessage);
        $clientNotified = $this->notificationService->notifyClient($event, $clientMessage);

        return [
            'reminder' => $reminder,
            'notifications' => [
                'technician_notified' => $technicianNotified,
                'client_notified' => $clientNotified,
            ],
        ];
    }

    protected function handlePanelFault(ConditionEvent $event): array
    {
        $technicianMessage = 'Panel fault detected. Inspection and repair are required.';
        $clientMessage = 'A panel fault has been detected in your solar system. A technician will inspect it.';

        $reminder = $this->createConditionReminder($event, 'urgent', $technicianMessage);

        $technicianNotified = $this->notificationService->notifyTechnician($event, $technicianMessage);
        $clientNotified = $this->notificationService->notifyClient($event, $clientMessage);

        return [
            'reminder' => $reminder,
            'notifications' => [
                'technician_notified' => $technicianNotified,
                'client_notified' => $clientNotified,
            ],
        ];
    }

    protected function handleInverterIssue(ConditionEvent $event): array
    {
        $technicianMessage = 'Inverter issue detected. Urgent diagnostics are required.';
        $clientMessage = 'An inverter issue has been detected in your solar system. Urgent attention is required.';

        $reminder = $this->createConditionReminder($event, 'critical', $technicianMessage);

        $technicianNotified = $this->notificationService->notifyTechnician($event, $technicianMessage);
        $clientNotified = $this->notificationService->notifyClient($event, $clientMessage);

        return [
            'reminder' => $reminder,
            'notifications' => [
                'technician_notified' => $technicianNotified,
                'client_notified' => $clientNotified,
            ],
        ];
    }

    protected function handleOverheating(ConditionEvent $event): array
    {
        $technicianMessage = 'Overheating detected. Immediate safety inspection required.';
        $clientMessage = 'Your solar system has reported overheating. Please avoid tampering while maintenance is arranged.';

        $reminder = $this->createConditionReminder($event, 'critical', $technicianMessage);

        $technicianNotified = $this->notificationService->notifyTechnician($event, $technicianMessage);
        $clientNotified = $this->notificationService->notifyClient($event, $clientMessage);

        return [
            'reminder' => $reminder,
            'notifications' => [
                'technician_notified' => $technicianNotified,
                'client_notified' => $clientNotified,
            ],
        ];
    }

    protected function normalizeEventType(ConditionEvent $event): ConditionEventType
    {
        if ($event->event_type instanceof ConditionEventType) {
            return $event->event_type;
        }

        return ConditionEventType::from($event->event_type);
    }

    protected function createConditionReminder(
        ConditionEvent $event,
        string $status,
        string $message
    ): Reminder {
        return Reminder::create([
            'installation_id' => $event->installation_id,
            'maintenance_plan_id' => null,
            'client_id' => $event->installation?->client_id,
            'technician_id' => $event->technician_id,
            'message' => $message,
            'due_date' => now(),
            'status' => $status,
            'trigger_source' => 'condition',
        ]);
    }
}