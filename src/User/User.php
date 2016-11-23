<?php

namespace RCorpWechat\User;

use RCorpWechat\Core\AbstractAPI;

class User extends AbstractAPI
{
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/get';
    const API_SIMPLELIST = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist';
    const API_DETAILLIST = 'https://qyapi.weixin.qq.com/cgi-bin/user/list';
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/user/update';
    const API_BATCH_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/user/delete';
    const API_OAUTH_GET = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo';
    const API_CONVERT_TO_USERID = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid';
    const API_CONVERT_TO_OPENID = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid';

    public function get($userid) {
        $params = [
            'userid' => $userid,
        ];

        return $this->parseJSON('get', [
            self::API_GET,
            $params,
        ]);
    }

    public function update($userid, array $userinfo = []) {
        $params = [
            'userid' => $userid,
        ];

        $params = array_merge($params, $userinfo);

        return $this->parseJSON('json', [
            self::API_UPDATE,
            $params,
        ]);
    }

    public function delete($userid) {
        $params = [
            'userid' => $userid,
        ];

        return $this->parseJSON('get', [
            self::API_DELETE,
            $params,
        ]);
    }

    public function batchDelete(array $userlist) {
        $params = [
            'userlist' => $userlist,
        ];

        return $this->parseJSON('json', [
            self::API_BATCH_DELETE,
            $params,
        ]);
    }

    public function create($userid, $name, $department, array $options = []) {
        $params = [
            'userid' => $userid,
            '$name' => $name,
            '$department' => $department,
        ];

        $params = array_merge($params, $options);

        return $this->parseJSON('json', [
            self::API_CREATE,
            $params,
        ]);
    }

    public function simplelist($department_id, $fetch_child = 0, $status = 0) {
        $params = [
            'department_id' => $department_id,
            'fetch_child' => $fetch_child,
            'status' => $status,
        ];

        return $this->parseJSON('get', [
            self::API_SIMPLELIST,
            $params,
        ]);
    }

    public function convertToUserid($openid, $agentid = null) {
        
    }

    public function convertToOpenid($userid) {
        
    }
}