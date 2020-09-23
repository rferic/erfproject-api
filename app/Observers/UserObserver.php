<?php

namespace App\Observers;

use App\Jobs\SendAdminUserCreatedMail;
use App\Models\User;

class UserObserver
{
    public function created ( User $user ): void
    {
        dispatch(new SendAdminUserCreatedMail($user));
    }
}
