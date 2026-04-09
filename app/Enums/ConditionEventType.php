<?php

namespace App\Enums;

enum ConditionEventType: string
{
    case LOW_VOLTAGE = 'low_voltage';
    case BATTERY_FAILURE = 'battery_failure';
    case PANEL_FAULT = 'panel_fault';
    case INVERTER_ISSUE = 'inverter_issue';
    case OVERHEATING = 'overheating';
}

