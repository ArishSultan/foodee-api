<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Redis;


/**
 * Class FileUploadHelper
 * @package App\Helpers
 */
class CustomBroadcaster
{

    /**
     * Custom broadcasting service
     * @param $user
     * @param $event
     * @param $data_
     */
    public static function fire($user, $event, $data_)
    {
        $channel = "user-global-" . $user;
        $data = [
            'event' => $event,
            'data' => $data_
        ];
        Redis::publish($channel, json_encode($data));
    }

}