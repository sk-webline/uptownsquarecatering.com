<?php

namespace App\Models\ApiClient;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Psr7\Uri;

class ZeroVendingApiMethods extends Model
{

    /**
     * Make a GET request to check if a rfid card exists.
     *
     * @param  int  $rfid
     * @return boolean
     */
    public static function get_organisations($token)
    {

        $client = new ApiClient;

        $url = config('app.zerovending.url').'/webservices/get_organisations?token='.config('app.zerovending.token');
        $response= $client->get($url,
            array_merge_recursive(
                [RequestOptions::HEADERS => ['Content-Type' => 'application/json', 'Accept'=> 'application/json']],
            ));
//        dd($url, $response, $response->getBody());

        return $response->getBody();


    }

    /**
     * Make a GET request to check if a rfid card exists.
     *
     * @param  int  $rfid
     * @return boolean
     */
    public static function get_organisation_cards($token, $organisation_id)
    {

        $client = new ApiClient;

        $url = config('app.zerovending.url')."/webservices/get_organisation_cards?token=".config('app.zerovending.token')."&organisation_id={$organisation_id}";

        $response= $client->get($url,
            array_merge_recursive(
                [RequestOptions::HEADERS => ['Content-Type' => 'application/json', 'Accept'=> 'application/json']],

            )) ;

        return $response->getBody();


    }


}
