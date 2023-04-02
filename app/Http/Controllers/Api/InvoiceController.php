<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use InvoiceProvidersHelper;
use App\Helpers\HttpClientHelper;

class InvoiceController extends Controller {

    public function callToService(Request $request) {

        $provider = $request->provider;
        $invoice_id = $request->invoice_id;

        $providers = config('invoiceprovides.providers'); //get registered provider list
        //Validate service providers 
        if (InvoiceProvidersHelper::isValidProvider($request->provider, $providers) === false) {
            return response()->json(["error" => "service provider not found"], 400);
        }

        $vars = array('$invoice_id' => $invoice_id);

        $provider_service_urls = config('invoiceprovides.providers_service_url');
        $provider_service_url_template = $provider_service_urls[$provider];

        $params = array();
        $params["url"] = $provider_service_url_template["service_url"] . "/" . $provider . "/" . $invoice_id;
        $params["request_timeout"] = $provider_service_url_template["request_timeout"];
        $params["connection_timeout"] = $provider_service_url_template["connection_timeout"];

        $response = HttpClientHelper::get($params);


        if ($response->ok()) {
            $dto = $this->formatDto($response);
            return response()->json($dto, $response->status());
        }

        return response()->json($response->json(), $response->status());
    }

    private function formatDto($response) {

        $dto = array();
        $dto["id"] = strval($response->json("Id"));
        $dto["invoice-nr"] = $response->json("InvoiceNumber");
        $dto["seller"] = array("name" => "", "organisation-number" => "");
        $dto["buyer"] = array(
            "name" => $response->json("InvoiceCustomerName"),
            "organisation-number" => $response->json("CustomerNumber"),
            "invoicing" => array(
                "email" => $response->json("CustomerEmail"),
                "address1" => $response->json("InvoiceAddress1"),
                "address2" => $response->json("InvoiceAddress2"),
                "zip-code" => $response->json("InvoicePostalCode"),
                "state" => $response->json("InvoiceCity"),
                "country" => $response->json("InvoiceCountryCode"),
            )
        );

        $dto["invoice-date"] = $response->json("InvoiceDate");
        $dto["due-date"] = $response->json("DueDate");
        $dto["delivery-date"] = null;
        $dto["currency"] = $response->json("CurrencyCode");
        $dto["currency-rate"] = false;
        $dto["sent"] = null;
        $dto["notes"] = null;
        $dto["country-code"] = $response->json("InvoiceCountryCode");
        $dto["amount"] = $response->json("TotalAmount");
        $dto["rows"] = array();

        $rows = $response->json("Row");
        $rows_arr = array();
        if(!empty($rows)) {
            foreach ($rows as $row) {

                $row_obj = array();
                $row_obj["id"] = $row["ArticleNumber"];
                $row_obj["quantity"] = $row["Quantity"];
                $row_obj["price"] = $row["UnitPrice"] * $row["Quantity"];
                $row_obj["vat"] = $row["PercentVat"];
                $row_obj["product-name"] = $row["Text"];
                $row_obj["unit"] = $row["UnitName"];
                $rows_arr[] = $row_obj;
            }
            $dto["rows"] = $rows_arr;
        }
        return $dto;
    }

}
