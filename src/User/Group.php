<?php

namespace RCorpWechat\User;

use RCorpWechat\Core\AbstractAPI;

class Group extends AbstractAPI
{
    const API_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/department/list';
    const API_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/create';
    const API_UPDATE = 'https://qyapi.weixin.qq.com/cgi-bin/department/update';
    const API_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/department/delete';


    public function create($name, $parentid = 1, $order = null, $id = null)
    {
        $params = [
            'name' => $name,
            'parentid' => $parentid,
            'order' => $order,
            'id' => $id,
        ];

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    public function update($id, $name, $parentid = 1, $order = null){
        $params = [
            'name' => $name,
            'parentid' => $parentid,
            'order' => $order,
            '$id' => $id,
        ];

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    public function delete($id){
        $params = [
            'id' => $id,
        ];

        return $this->parseJSON('get', [self::API_DELETE, $params]);
    }

    public function lists($id){
        $params = [
            'id' => $id,
        ];

        return $this->parseJSON('get', [self::API_LIST, $params]);
    }
}