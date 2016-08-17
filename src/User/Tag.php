<?php

namespace RCorpWechat\User;

use RCorpWechat\COre\AbstractAPI;

class Tag extends AbstractAPI
{
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/update';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/tag/delete';
    const API_GET = 'https://qyapi.weixin.qq.com/cgi-bin/tag/get';
    const API_ADD_TAG_USERS = 'https://qyapi.weixin.qq.com/cgi-bin/tag/ADDTAGUSERS';
    const API_DELETE_TAG_USERS = 'https://qyapi.weixin.qq.com/cgi-bin/tag/deltagusers';
    const API_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/tag/list';


    public function create($name)
    {
        $params = [
            'tagname' => $name,
        ];

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    public function update($tagid, $name)
    {
        $params = [
            'tagid' => $tagid,
            'tagname' => $name,
        ];

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    public function delete($tagid)
    {
        $params = [
            'tagid' => $tagid,
        ];

        return $this->parseJSON('get', [self::API_DELETE, $params]);
    }

    public function get($tagid)
    {
        $params = [
            'tagid' => $tagid,
        ];

        return $this->parseJSON('get', [self::API_GET, $params]);
    }

    public function addtagusers($tagid, $userlist, $partylist)
    {
        $params = [
            'tagid' => $tagid,
            'userlist' => $userlist,
            'partylist' => $partylist,
        ];

        return $this->parseJSON('json', [self::API_ADD_TAG_USERS, $params]);
    }

    public function deltagusers($tagid, $userlist, $partylist)
    {
        $params = [
            'tagid' => $tagid,
            'userlist' => $userlist,
            'partylist' => $partylist,
        ];

        return $this->parseJSON('json', [self::API_DELETE_TAG_USERS, $params]);
    }

    public function lists()
    {
        return $this->parseJSON('get', [self::API_GET]);
    }
}