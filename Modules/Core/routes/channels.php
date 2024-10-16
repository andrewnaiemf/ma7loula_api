<?php

use Illuminate\Support\Facades\Broadcast;
use Modules\Core\Broadcasting\UserChannel;

Broadcast::channel('user.{user_id}', UserChannel::class);