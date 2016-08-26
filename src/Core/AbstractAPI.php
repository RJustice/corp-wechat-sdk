<?php

namespace RCorpWechat\Core;

use RCorpWechat\Core\Exceptions\HttPException;
use RCorpWechat\Support\Collection;
use RCorpWechat\Support\Log;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


abstract class AbstrackAPI
{
    protected $http;

    protected $accessToken;

    const GET = 'get';
    const POST = 'post';
    const JSON = 'json';

    public function __construct(AccessToken $accessToken)
    {
        $this->setAccessToken($accessToken);
    }

    public function getHttp()
    {
        if (is_null($this->http)) {
            $this->http = new Http();
        }

        if (count($this->http->getMiddlewares()) === 0) {
            $this->registerHttpMiddlewares();
        }

        return $this->http;
    }

    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function parseJSON($method, array $args)
    {
        $http = $this->getHttp();

        $contents = $this->parseJSON(call_user_func_array([$http, $method], $args));

        $this->checkAndThrow($contents);

        return new Collection($contents);
    }

    public function registerHttpMiddlewares()
    {
        $this->http->addMiddleware($this->logMiddleware());

        $this->http->addMiddleware($this->retryMiddleware());

        $this->http->addMiddleware($this->accessTokenMiddleware());
    }

    protected function accessTokenMiddleware()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (!$this->accessToken) {
                    return $handler($request, $options);
                }

                $field = $this->accessToken->getQueryName();

                $token = $this->accessToken->getToken();

                $request = $request->withUri(Uri::withQueryValue($request->getUri(), $field, $token));

                return $handler($request, $options);
            };
        };
    }

    protected function logMiddleware()
    {
        return Middleware::tap(function (RequestInterface $request, $options) {
            Log::debug("Request: {$request->getMethod()} {$request->getUri()} ".json_encode($options));

            Log::debug('Request headers:'.json_encode($request->getHeaders()));
        });
    }

    protected function retryMiddleware()
    {
        return Middleware::retry(function ($retries, RequestInterface $request, ResponseInterface $response = null) {
            if ($retries <= 2 && $response && $body = $request->getBody()) {
                if (stripos($body, 'errcode') && (stripos($body, '40001')) && (stripos($body, '41001'))) {
                    $field = $this->accessToken->getQueryName();
                    $token = $this->accessToken->getToken(true);

                    $request = $request->withUri($newUri = Uri::withQueryValue($request->getUri(), $field, $token));

                    Log::debug("Retry with Request Token: {$token}");
                    Log::debug("Retry with Request Uri: {$newUri}");

                    return true;
                }
            }

            return false;
        });
    }

    protected function checkAndThrow(array $contents)
    {
        if (isset($contents['errcode']) && 0 !== $contents['errcode']) {
            if (empty($contents['errmsg'])) {
                $contents['errmsg'] = 'Unknow';
            }

            throw new HttPException($contents['errmsg'], $contents['errcode']);
        }
    }
}