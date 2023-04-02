<?php

namespace App\Helpers;

class InvoiceProvidersHelper {

    public static function isValidProvider(string $provider , array $providers  ) {
        return in_array($provider, $providers);
    }
}
