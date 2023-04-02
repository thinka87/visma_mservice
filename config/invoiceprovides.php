<?php

if (env('APP_ENV') == 'local') {
    return [
        'providers' => array("vizma"),
        'providers_service_url' => array(
            "vizma" => array('service_url' => 'https://qa-app.capcito.com/api/v3/work-sample/invoices', 'connection_timeout' => 60, 'request_timeout' => 60),
        ),
    ];
}

if (env('APP_ENV') == 'staging') {
    return [
        'providers' => array("vizma"),
        'providers_service_url' => array(
            "vizma" => array('service_url' => 'https://qa-app.capcito.com/api/v3/work-sample/invoices', 'connection_timeout' => 60, 'request_timeout' => 60),
        ),
    ];
}
if (env('APP_ENV') == 'production') {
    return [
        'providers' => array("vizma"),
        'providers_service_url' => array(
            "vizma" => array('service_url' => 'https://qa-app.capcito.com/api/v3/work-sample/invoices', 'connection_timeout' => 60, 'request_timeout' => 60),
        ),
    ];
}