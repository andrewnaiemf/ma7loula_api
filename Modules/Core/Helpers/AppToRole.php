<?php

namespace Modules\Core\Helpers;

class AppToRole
{
    private const APP = [
        'client' => 2,
        'cp-vendor' => 3,
        'bt-vendor' => 4,
        'winch-driver' => 5,
        'bt-worker' => 6,
        'sos-worker' => 7
    ];

    public static function getRoleId($app = null)
    {
        return self::APP[$app ?? 'client'] ?? 2;
    }
}
