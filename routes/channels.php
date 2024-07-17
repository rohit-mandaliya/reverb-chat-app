<?php

use Illuminate\Support\Facades\Broadcast;

//Broadcast::channel('private_user.{id}', function ($user, $id) {
//    info('event called');
//    return (int)$user->id === (int)$id;
//});

Broadcast::channel('send-message.{userId}', function (\App\Models\User $user, $userId) {
    return (int)$user->id === (int)$userId;
});

Broadcast::channel('is-typing.{userId}', function (\App\Models\User $user, $userId) {
    return (int)$user->id === (int)$userId;
});
