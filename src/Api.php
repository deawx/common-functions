<?php

namespace Croga\Functions;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\NoReturn;
use Croga\Functions\Traits\Error;

/**
 * Class Api
 *
 * @package Croga\Functions
 */
class Api
{

    use Error;


    /**
     * Display json response
     *
     * @param array $packet ?format to display html
     * @param int   $response_code
     */
    #[NoReturn] public static function jsonDisplay(array $packet, int $response_code = 200): void
    {
        @ob_clean();
        http_response_code($response_code);
        header('Content-Type: application/json');
        echo Json::encode($packet);
        die();
    }

    /**
     * Display text
     *
     * @param string $data
     * @param int    $status_code
     */
    #[NoReturn] public static function textDisplay(string $data, int $status_code = 200): void
    {
        @ob_clean();
        http_response_code($status_code);
        header('Content-Type: text/plain');
        echo $data;
        die();
    }


    /**
     * Display text
     *
     * @param object|array|string $data
     */
    #[NoReturn] public static function printR(object|array|string $data): void
    {
        @ob_clean();
        header('Content-Type: text/plain');
        echo '<pre>';
        print_R($data);
        die();
    }


    /**
     * Gets input
     *
     * @return string|bool
     */
    public static function getInput(): string|bool
    {
        return file_get_contents('php://input');
    }


    /**
     * Guzzle get API request
     *
     * @param string $url
     * @param string $method
     * @param array  $body
     * @param array  $digest_auth    [ username => '', password => '', type => 'digest' ]
     * @param array  $custom_headers [ 'header1' => value, header2 => value2 ]
     * @param array  $request_options
     * @param bool   $sentry_capture_errors
     *
     * @return object
     */

    #[ArrayShape(['success' => "bool", 'code' => "int", 'body' => 'object', 'raw_body' => 'string'])]
    public static function apiRequest(
        string $url,
        string $method = 'GET',
        array $body = [],
        array $digest_auth = [],
        array $custom_headers = [],
        array $request_options = [],
        bool $sentry_capture_errors = false
    ): object {
        try
        {
            $options = [
                'http_errors' => true
            ];

            if( ! empty($request_options) )
            {
                $options = array_replace_recursive($options, $request_options);
            }

            if (!empty($body))
            {
                $options['json'] = $body;
                /* 'headers' => ['Content-Type' => 'application/json'],
                   'body' => json_encode([
                               'name' => 'Example name',
                           ])*/
            }

            if (!empty($digest_auth))
            {
                $options['auth'] = [
                    $digest_auth['username'],
                    $digest_auth['password'],
                    ($digest_auth['type'] ?? 'digest')
                ];
            }

            $options['headers'] = [
                'Accept' => 'application/json'
            ];

            if (!empty($custom_headers))
            {
                $options['headers'] = array_replace($options['headers'], $custom_headers);
            }

            $client      = new \GuzzleHttp\Client();
            $response    = $client->request(strtoupper($method), $url, $options);
            $result_body = $response->getBody()->getContents();
            $result_body = Json::decodeToObject($result_body);

            return (object)[
                'success'  => in_array($response->getStatusCode(), [200, 201]),
                'code'     => $response->getStatusCode(),
                'body'     => $result_body,
                'raw_body' => Json::encode($result_body),
            ];
        } catch (
            ClientException|
            RequestException|
            BadResponseException|
            GuzzleException|
            TransferException|
            ServerException|
            \RuntimeException|
            \Exception
        $e)
        {
            if ( $sentry_capture_errors && function_exists('\Sentry\captureException'))
            {
                /** @noinspection PhpUndefinedFunctionInspection */
                /** @noinspection PhpUndefinedNamespaceInspection */
                \Sentry\captureException($e);
            }

            return (object)[
                'success'  => false,
                'code'     => $e->getCode(),
                'body'     => $e->getMessage(),
                'raw_body' => $e->getMessage()
            ];
        }
    }


}