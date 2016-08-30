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

    const TICKET_CACHE_PREFIX = 'rcorpwechat.jsapi_ticket.';
    const API_TICKET = 'https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket';
    const GROUP_TICKET_CACHE_PREFIX = 'rcorpwechat.group.jsapi_ticket';
    const GROUP_API_TICKET = 'https://qyapi.weixin.qq.com/cgi-bin/ticket/get';

    public function config(array $APIs, $debug = false, $beta = false, $json = true) {
        $signPackage = $this->signature();

        $base = [
            'debug' => $debug,
            'beta' => $beta,
        ];

        $config = array_merge($base, $signPackage, ['jsAPIList' => $APIs]);

        return $json ? json_encode($config) : $config;
    }

    public function contactConfig(array $APIs, $debug = false, $beta = false, $json = true) {
        $signPackage = $this->groupSignature();

        $base = [
            'debug' => $debug,
            'beta' => $beta,
        ];

        $config = array_merge($base, $signPackage, ['jsaAPIList' => $APIs]);

        return $json ? json_encode($config) : $config;
    }

    public function getConfigArray(array $APIs, $debug = false, $beta = false) {
        return $this->config($APIs, $debug, $beta, false);
    }

    public function ticket() {
        $key = self::TICKET_CACHE_PREFIX . $this->getAccessToken()->getCorpId();

        if ($ticket = $this->getCache()->fetch($key)) {
            return $ticket;
        }

        $result = $this->parseJSON('get', [self::API_TICKET]);

        $this->getCache()->save($key, $result['ticket'], $result['expires_in'] - 500);

        return $result['ticket'];
    }

    public function groupTicket() {
        $ticketKey = self::GROUP_TICKET_CACHE_PREFIX . $this->getAccessToken()->getCorpId();
        $groupKey = self::GROUP_TICKET_CACHE_PREFIX . $this->getAccessToken()->getCorpId() . '.group_id';

        if ($ticket = $this->getCache()->fetch($ticketKey)) {
            return $ticket;
        }

        $result = $this->parseJSON('get', [self::GROUP_API_TICKET, ['type' => 'contact']]);

        $this->getCache()->save($ticketKey, $result['ticket'], $result['expires_in'] - 500);
        $this->getCache()->save($groupKey, $result['group_id'], $result['expires_in'] - 500);

        return $result['ticket'];
    }

    public function groupId() {
        $groupKey = self::GROUP_TICKET_CACHE_PREFIX . $this->getAccessToken()->getCorpId() . '.group_id';
        $ticketKey = self::GROUP_TICKET_CACHE_PREFIX . $this->getAccessToken()->getCorpId();

        if ($groupId = $this->getCache()->fetch($groupKey)) {
            return $groupId;
        }

        $result = $this->parseJSON('get', [self::GROUP_API_TICKET, ['type' => 'contact']]);

        $this->getCache()->save($groupKey, $result['group_id'], $result['expires_in'] - 500);
        $this->getCache()->save($ticketKey, $result['ticket'], $result['expires_in'] - 500);

        return $result['group_id'];
    }

    public function signature($url = null, $nonce = null, $timestamp = null) {
        $url = $url ? $url : $this->getUrl();
        $nonce = $nonce ? $nonce : Str::quickRandom(10);
        $timestamp = $timestamp ? $timestamp : time();
        $ticket = $this->ticket();

        $sign = [
            'corpId' => $this->getAccessToken()->getCorpId(),
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getSignature($ticket, $nonce, $timestamp, $url),
        ];

        return $sign;
    }

    public function groupSignature($url = null, $nonce = null, $timestamp = null) {
        $url = $url ? $url : $this->getUrl();
        $nonce = $nonce ? $nonce : Str::quickRandom(10);
        $timestamp = $timestamp ? $timestamp : time();
        $ticket = $this->groupTicket();

        $sign = [
            'corpId' => $this->getAccessToken()->getCorpId(),
            'nonceStr' => $nonce,
            'timestamp' => $timestamp,
            'url' => $url,
            'signature' => $this->getGroupSignature($ticket, $nonce, $timestamp, $url),
        ];

        return $sign;
    }

    public function getSignature($ticket, $nonce, $timestamp, $url) {
        return sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }

    public function getGroupSignature($ticket, $nonce, $timestamp, $url) {
        return sha1("group_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }

    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    public function getUrl() {
        if ($this->url) {
            return $this->url;
        }

        return UrlHelper::current();
    }

    public function setCache(Cache $cache) {
        $this->cache = $cache;

        return $this;
    }

    public function getCache() {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }
}