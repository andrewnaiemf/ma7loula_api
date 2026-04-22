<?php

namespace App\Enums;

enum OrderStatus: string
{
    case New = 'new';
    case PendingCustomer = 'pending_customer';
    case PendingVendor = 'pending_vendor';
    case Accepted = 'accepted';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
