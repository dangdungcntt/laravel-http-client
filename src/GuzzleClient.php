<?php

namespace Nddcoder\HttpClient;

use Exception;
use Illuminate\Log\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class GuzzleClient implements HttpClient
{
    /* @var Logger $logger */
    protected $logger;

    /* @var Client $client */
    protected $client;


    public function __construct(Client $client, Logger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function request($method, $url, $options = [])
    {
        try {
            $response = $this->client->request($method, $url, $options);
        } catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $this->logger->error('HTTP REQUEST FAILED');
            $this->logger->error($exception);
        } catch (Exception $e) {
            $this->logger->error('UNKNOWN EXCEPTION');
            $this->logger->error($exception);
            $response = null;
        } finally {
            $body = optional($response)->getBody();
            $content = optional($body)->getContents();

            $responseData = [
                'status_code' => optional($response)->getStatusCode(),
                'headers' => optional($response)->getHeaders(),
                'body' => $content,
            ];

            if (data_get($options, 'json_response', true) == true) {
                $responseData['bodyJSON'] = json_decode($content, true);
            }

            $this->logger->debug('HTTP REQUEST SENT', [
                'url' => $url,
                'method' => $method,
                'headers' => data_get($options, 'headers'),
                'auth' => data_get($options, 'auth'),
                'request_options' => $options,
                'response' => $responseData,
            ]);

            return $responseData;
        }
    }

    public function get($url, $options = [])
    {
        return $this->request('GET', $url, $options);
    }

    public function post($url, $options = [])
    {
        data_fill($options, 'headers.Content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
        data_fill($options, 'headers.Cache-control', 'no-cache');

        return $this->request('POST', $url, $options);
    }

    public function put($url, $options = [])
    {
        data_fill($options, 'headers.Content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
        data_fill($options, 'headers.Cache-control', 'no-cache');

        return $this->request('PUT', $url, $options);
    }

    public function delete($url, $options = [])
    {
        return $this->request('DELETE', $url, $options);
    }
}

