<?php

namespace App\Gateways\VivaWallet;

use InvalidArgumentException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use App\Gateways\VivaWallet\VivaException;
use stdClass;

class Client
{


    /**
     * Demo environment URL.
     */
    const DEMO_URL = 'https://demo.vivapayments.com';

    /**
     * Production environment URL.
     */
    const PRODUCTION_URL = 'https://www.vivapayments.com';

    /**
     * Demo environment URL.
     */
    const DEMO_API_URL = 'https://demo-api.vivapayments.com';

    /**
     * Production environment URL.
     */
    const PRODUCTION_API_URL = 'https://api.vivapayments.com';

    /**
     * Demo environment accounts URL.
     */
    const DEMO_ACCOUNTS_URL = 'https://demo-accounts.vivapayments.com';

    /**
     * Production environment accounts URL.
     */
    const PRODUCTION_ACCOUNTS_URL = 'https://accounts.vivapayments.com';

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    private $bearer_token;


    /**
     * Constructor.
     */
    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
        $environment = config('gateways.viva.environment');
        if (! in_array($environment, ['demo', 'production'])) {
            throw new InvalidArgumentException(
                'The Viva Payments environment must be demo or production.'
            );
        }

        $this->environment = $environment;

        $this->generateToken();
    }

    private function generateToken() {

        $parameters = ['grant_type' => 'client_credentials'];

        $response = $this->post(
            $this->getAccountsUrl()->withPath('/connect/token'),
            array_merge([
                RequestOptions::FORM_PARAMS => $parameters,
                RequestOptions::AUTH => [config('gateways.viva.client_id'), config('gateways.viva.client_secret')]
            ])
        );

        $this->bearer_token = $response->access_token;
    }

    /**
     * Make a GET request.
     *
     * @param  string  $url
     * @param  array  $options
     * @return stdClass
     */
    public function get(string $url, array $options = [])
    {
        $response = $this->client->get($url, $options);

        return $this->getBody($response);
    }

    /**
     * Make a POST request.
     *
     * @param  string  $url
     * @param  array  $options
     * @return stdClass
     */
    public function post(string $url, array $options = [])
    {
        $response = $this->client->post($url, $options);

        return $this->getBody($response);
    }

    /**
     * Get the response body.
     *
     * @param  ResponseInterface  $response
     * @return stdClass|null
     *
     * @throws VivaException
     */
    protected function getBody(ResponseInterface $response)
    {
        /** @var stdClass|null $body */
        $body = json_decode($response->getBody(), false, 512, JSON_BIGINT_AS_STRING);

        if (isset($body->ErrorCode) && $body->ErrorCode !== 0) {
            throw new VivaException($body->ErrorText, $body->ErrorCode);
        }

        return $body;
    }

    /**
     * Get the URL.
     */
    public function getUrl(): UriInterface
    {
        $uris = [
            'production' => self::PRODUCTION_URL,
            'demo' => self::DEMO_URL,
        ];

        return new Uri($uris[$this->environment]);
    }

    /**
     * Get the API URL.
     */
    public function getApiUrl(): UriInterface
    {
        $uris = [
            'production' => self::PRODUCTION_API_URL,
            'demo' => self::DEMO_API_URL,
        ];

        return new Uri($uris[$this->environment]);
    }

    /**
     * Get the accounts URL.
     */
    public function getAccountsUrl(): UriInterface
    {
        $uris = [
            'production' => self::PRODUCTION_ACCOUNTS_URL,
            'demo' => self::DEMO_ACCOUNTS_URL,
        ];

        return new Uri($uris[$this->environment]);
    }

    /**
     * Authenticate using the bearer token as an authorization header.
     */
    public function authenticateWithBearerToken(): array
    {
        return [
            RequestOptions::HEADERS => [
                'Authorization' => "Bearer $this->bearer_token",
            ],
        ];
    }

    /**
     * Authenticate using the basic token as an authorization header
     * client id
     * client secret
     *
     */
    public function authenticateWithBasicToken(): array
    {
        return [
            RequestOptions::HEADERS => [
                'Authorization' => "Basic ".base64_encode(config('gateways.viva.client_id').':'.config('gateways.viva.client_secret')),
            ],
        ];
    }

}
