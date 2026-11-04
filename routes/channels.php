<?php

use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('crm.live-location', function ($user) {
    // Replace with your CRM's real authorization logic.
    // Only users explicitly permitted to view live tracking
    // should be allowed to subscribe.
    return in_array((int) $user->role_id, [
        AppHelper::USER_SUPER_ADMIN,
        AppHelper::USER_ADMINISTRATOR,
        AppHelper::USER_ADMIN,
        AppHelper::USER_DIRECTOR,
        AppHelper::USER_MANAGER,
        AppHelper::USER_RSM,
        AppHelper::USER_ASM,
        AppHelper::USER_SUP,
    ], true);
});
