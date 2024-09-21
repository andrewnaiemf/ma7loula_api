<?php

namespace Modules\Core\Observers;

use App\Models\Client;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $appToRole = [
            'client' => 'client'
        ];

        $app = request()->header('app');

        $user->attachRole($appToRole[$app] ?? 'client');

        switch ($app) {
            case 'client':
                Client::create([
                    'user_id' => $user->id
                ]);
                break;
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // ...
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // ...
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // ...
    }

    /**
     * Handle the User "forceDeleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // ...
    }
}
