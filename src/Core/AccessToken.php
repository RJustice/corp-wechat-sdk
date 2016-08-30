<?php

namespace RCorpWechat\Core;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use RCorpWechat\Core\Exceptions\HttpException;

class AccessToken
{
    protected $corpid;

    protected $corpsecret;

    protected $cache;

    protected $cacheKey;

    protected $http;

    protected $queryName = 'access_token';

    protected $prefix = 'rcorpwechat.common.access_token.';

    const API_TOKEN_GET = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';

    public function __construct($corpid, $corpsecret, Cache $cache = null)
    {
        $this->corpid = $corpid;
        $this->corpsecret = $corpsecret;
        $this->cache = $cache;
    }

    public function getToken($forceRefresh = false)
    {
        $cacheKey = $this->getCacheKey();
        $cached = $this->getCache()->fetch($cacheKey);

        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();

            $this->getCache()->save($cacheKey, $token['access_token'], $token['expires_in'] - 1000);

            return $token['access_token'];
        }

        return $cached;
    }

    public function getCorpId()
    {
        return $this->corpid;
    }

    public function getCorpSecret()
    {
        return $this->corpsecret;
    }

    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache()
    {
        return $this->cache ? : $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    public function setQueryName($queryName)
    {
        $this->queryName = $queryName;

        return $this;
    }

    public function getQueryName()
    {
        return $this->queryName;
    }

    public function getQueryFields()
    {
        return [$this->queryName => $this->getToken()];
    }

    public function getTokenFromServer()
    {
        $params = [
            'corpid' => $this->corpid,
            'corpsecret' => $this->corpsecret,
        ];

        $http = $this->getHttp();

        $token = $http->parseJSON($http->get(self::API_TOKEN_GET, $params));

        if (empty($token['access_token'])) {
            throw new HttpException('Request AccessToken fail. response: '.json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }

    public function getHttp()
    {
        return $this->http ? $this->http : $this->http = new Http();
    }

    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    public function getCacheKey()
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix.$this->corpid;
        }

        return $this->cacheKey;
    }
}