<?php

namespace App\Gateways\VivaWallet;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;

class Order
{
    const PENDING = 0;
    const EXPIRED = 1;
    const CANCELED = 2;
    const PAID = 3;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Constructor.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a payment order.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders/post
     *
     * @param  int  $amount  The requested amount in the currency's smallest unit of measurement.
     * @param  array  $parameters  Optional parameters
     * @param  array  $guzzleOptions  Additional parameters for the Guzzle client
     * @return int
     */
    public function create(
        int $amount,
        array $parameters = [],
        array $guzzleOptions = []
    ) {
        $parameters = array_merge_recursive(['amount' => $amount], $parameters);

        $response = $this->client->post(
            $this->client->getApiUrl()->withPath('/checkout/v2/orders'),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBearerToken()
            )
        );

        return $response->orderCode;
    }

    /**
     * Retrieve information about an order.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders/post
     *
     * @param  int  $orderCode  The 16-digit orderCode for which you wish to retrieve information.
     * @param  array  $guzzleOptions  Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function get($orderCode, array $guzzleOptions = [])
    {

        return $this->client->get(
            $this->client->getUrl()->withPath("/api/orders/" . $orderCode),
            array_merge_recursive(
                $this->client->authenticateWithBasicToken(),
                $guzzleOptions
            )
        );
    }

    /**
     * Get the checkout URL for an order.
     *
     * @param int $orderCode
     */
    public function getCheckoutUrl(int $orderCode): UriInterface
    {
        return Uri::withQueryValue(
            $this->client->getUrl()->withPath('/web/checkout'),
            'ref',
            (string) $orderCode
        );
    }
}
