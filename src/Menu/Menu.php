<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/9/21
 * Time: 16:19
 */

namespace RCorpWechat\Menu;

use RCorpWechat\Core\AbstractAPI;

class Menu extends AbstractAPI
{
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/create';
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/menu/get';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/delete';

    public function all($agentId) {
        $params = [
            'agentid' => $agentId,
        ];

        return $this->parseJSON('get', [self::API_GET]);
    }

    public function create(array $buttons, $agentId) {
        $api = self::API_CREATE . '?agentid=' . $agentId;

        return $this->parseJSON('json', [
            $api,
            ['button' => $buttons],
        ]);
    }

    public function delete($agentId) {
        return $this->parseJSON('get', [
            self::API_DELETE,
            ['agentid' => $agentId],
        ]);
    }
}