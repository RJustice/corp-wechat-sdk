<?php

namespace RCorpWechat\Agent;

use RCorpWechat\Core\AbstractAPI;

class Agent extends AbstractAPI
{
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get';
    const API_SET = 'https://qyapi.weixin.qq.com/cgi-bin/agent/set';
    const API_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/agent/list';

    public function get($agentid)
    {
        $params = [
            'agentid' => $agentid,
        ];

        return $this->parseJSON('get', [self::API_GET, $params]);
    }

    public function set($agentid, $agentinfo){
        $params = [
            'agentid' => $agentid,
        ];

        $params = array_merge($params,$agentinfo);

        return $this->parseJSON('json', [self::API_SET, $params]);
    }

    public function lists()
    {
        return $this->parseJSON('get', [self::API_LIST]);
    }
}