<?php

namespace App\Models\ApiClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Uri;
use http\Message;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Whoops\Handler\PrettyPageHandler;
use GuzzleHttp\Exception\BadResponseException;

class ApiClient
{

    /**
     * @var GuzzleClient
     */
    protected $client;

    /**
     * @var string - the basis api url
     */
    protected $environment;
    protected $headers;

    protected $authorizationToken;

    /**
     * Constructor.
     */
    public function __construct()
    {

        $environment = config('app.zerovending.url').'/webservices/';

//        $this->authorizationToken = '-WtVB4C5aawuQ6E5j_K64ktjclT0WXSnwJ_4zoGnGSojvKJyTB6nLlPXnHpAkFyU0';

        $this->headers=[
//            'Authorization' => 'Bearer ' .$this->authorizationToken,
            'http_errors' => false
        ];

        $this->environment = $environment;

        $this->client = new GuzzleClient([
            'accept' => 'application/json',
            'headers' => $this->headers,
            'verify'  => false, // Disable SSL verification
        ]);

    }

    /**
     * Get the base URL.
     */
    public function getBaseUrl(): UriInterface
    {

        return new Uri($this->environment);
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

//        return $response = $this->client->get($url, $options);
//
//        return $this->getBody($response);

        try {
//            return 7;
            $response = $this->client->get($url, $options);
            return $response;
        }catch (BadResponseException $e) {
            return $e->getResponse();
        }catch(ClientException | RequestException | ServerException $e) {
            return $e->getResponse();
        }


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
        try {
            $response = $this->client->post($url, $options);
            return $response;
        }catch (BadResponseException $e) {
            return $e->getResponse();
        }catch(ClientException | RequestException | ServerException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Make a PUT request.
     *
     * @param  string  $url
     * @param  array  $options
     * @return stdClass
     */
    public function put(string $url, array $options = [])
    {
        try {
            $response = $this->client->put($url, $options);
            return $response;
        }catch (BadResponseException $e) {
            return $e->getResponse();
        }catch(ClientException | RequestException | ServerException $e) {
            return $e->getResponse();
        }
    }

    /**
     * Get the response body.
     *
     * @param  ResponseInterface  $response
     * @return stdClass|null
     *
     */
    protected static function getBody(ResponseInterface $response)
    {
        /** @var stdClass|null $body */


        $body = json_decode($response->getBody(), false, 512, JSON_BIGINT_AS_STRING);
        return $body;

    }



}
