<?php

namespace Nddcoder\HttpClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Log\Logger;

class GuzzleClient implements HttpClient
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function get($url, $options = [])
    {
        return $this->request('GET', $url, $options);
    }

    public function request($method, $url, $options = [])
    {
        try {
            $client = new Client();
            $response = $client->request($method, $url, $options);
        } catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $this->logger->error('HTTP REQUEST FAILED');
            $this->logger->error($exception);
        } catch (GuzzleException $e) {
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
