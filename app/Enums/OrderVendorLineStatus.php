<?php

namespace App\Enums;

enum OrderVendorLineStatus: string
{
    case New = 'new';
    case VendorAccepted = 'vendor_accepted';
    case OfferPending = 'offer_pending';
    case OfferDeclined = 'offer_declined';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
