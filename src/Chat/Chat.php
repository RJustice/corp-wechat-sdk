<?php

namespace RCorpWechat\Chat;

use RCorpWechat\Core\AbstractAPI;

class Chat extends AbstractAPI
{
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/chat/create';
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/chat/get';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/chat/update';
    const API_QUIT = 'https://qyapi.weixin.qq.com/cgi-bin/chat/quit';
    const API_CLEARNOTIFY = 'https://qyapi.weixin.qq.com/cgi-bin/chat/clearnotify';
    const API_SEND = 'https://qyapi.weixin.qq.com/cgi-bin/chat/send';
    const API_SETMUTE = 'https://qyapi.weixin.qq.com/cgi-bin/chat/setmute';

    public function create($chatId, $name, $owner, array $userlist) {
        $params = [
            'chatid' => $chatId,
            'name' => $name,
            'owner' => $owner,
            'userlist' => $userlist,
        ];

        return $this->parseJSON('json', [
            self::API_CREATE,
            $params,
        ]);
    }

    public function get($chaiId) {
        $params = [
            'chatid' => $chaiId,
        ];

        return $this->parseJSON('get', [
            self::API_GET,
            $params,
        ]);
    }

    public function update($chatid, $op_user, array $options = []) {
        $params = [
            'chatid' => $chatid,
            'op_user' => $op_user,
        ];

        if (in_array('name', $options)) {
            $params['name'] = $options['name'];
        }

        if (in_array('owner', $options)) {
            $params['owner'] = $options['owner'];
        }

        if (in_array('add_user_list', $options)) {
            $params['add_user_list'] = $options['add_user_list'];
        }

        if (in_array('del_user_list', $options)) {
            $params['del_user_list'] = $options['del_user_list'];
        }

        return $this->parseJSON('json', [
            self::API_UPDATE,
            $params,
        ]);
    }

    public function quit($chatid, $op_user) {
        $params = [
            'chatid' => $chatid,
            'op_user' => $op_user,
        ];

        return $this->parseJSON('json', [
            self::API_QUIT,
            $params,
        ]);
    }

}