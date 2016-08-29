<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/29
 * Time: 13:52
 */

namespace RCorpWechat\Js;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use RCorpWechat\Core\AbstractAPI;
use RCorpWechat\Support\Str;
use RCorpWechat\Support\Url as UrlHelper;

class Js extends AbstractAPI
{
    protected $cache;
    protected $url;

    const  TICKET_CACHE_PREFIX = 'rcorpwechat.jsapi_ticker.';
    const API_TICKET = 'https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket';

    public function config(array $APIs, $debug = false, $beta = false, $json = true) {
        $signPackage = $this->signature();

        $base = [
            'debug' => $debug,
            'beta' => $beta,
        ];

        $config = array_merge($base, $signPackage, ['jsAPIList' => $APIs]);

        return $json ? json_encode($config) : $config;
    }

    public function getConfigArray(array $APIs, $debug = false, $beta = false) {
        return $this->config($APIs, $debug, $beta, false);
    }

    public function ticket() {
        $key = self::TICKET_CACHE_PREFIX . $this->getAccessToken()->getAppId();

        if ($ticket = $this->getCache()->fetch($key)) {
            return $ticket;
        }

        $result = $this->parseJSON('get', [self::API_TICKET]);

        $this->getCache()->save($key, $result['ticket'], $result['expires_in'] - 500);

        return $result['ticket'];
    }

    public function signature($url = null, $nonce = null, $timestamp = null) {
        $url = $url ? $url : $this->getUrl();
        $nonce = $nonce ? $nonce : Str::quickRandom(10);
        $timestamp = $timestamp ? $timestamp : time();

        $sign = [
            'corpId' => $this->getAccessToken()->getCorpId(),
        ];
    }
}