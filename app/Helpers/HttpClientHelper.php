<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class HttpClientHelper {

    public static function get($params) {

        $response = Http::withOptions(
                        [
                            'verify' => false,
                            'timeout' => $params["request_timeout"],
                            'connect_timeout' => $params["connection_timeout"]
                        ]
                )
                ->acceptJson()
                ->get($params["url"]);
        return $response;
    }

}
