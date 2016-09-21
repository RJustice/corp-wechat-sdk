<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/30
 * Time: 14:36
 */

namespace RCorpWechat\Card;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use RCorpWechat\Core\AbstractAPI;
use RCorpWechat\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use RCorpWechat\Support\Collection;

class Card extends AbstractAPI
{
    protected $cache;

    const TICKET_CACHE_PREFIX = 'rcorpwechat.card_api_ticket.';

    const API_CREATE_CARD = 'https://qyapi.weixin.qq.com/cgi-bin/card/create';
    const API_GET_CARD = 'https://qyapi.weixin.qq.com/cgi-bin/card/get';
    const API_GET_CARD_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/card/batchget';
    const API_MODIFY_STOCK = 'https://qyapi.weixin.qq.com/cgi-bin/card/modifystock';
    const API_DELETE_CARD = 'https://qyapi.weixin.qq.com/cgi-bin/card/delete';
    const API_SEND_CARD = 'https://qyapi.weixin.qq.com/cgi-bin/message/send';
    const API_GET_HTML = 'https://qyapi.weixin.qq.com/cgi-bin/card/mpnews/gethtml';
    const API_CREATE_QRCODE = 'https://qyapi.weixin.qq.com/cgi-bin/card/qrcode/create';
    const API_GET_CODE = 'https://qyapi.weixin.qq.com/cgi-bin/card/code/get';
    const API_CONSUME_CODE = 'https://qyapi.weixin.qq.com/cgi-bin/card/code/consume';
    const API_GET_TICKET = 'https://qyapi.weixin.qq.com/cgi-bin/ticket/get';

    public function ticket() {
        $key = self::TICKET_CACHE_PREFIX . $this->getAccessToken()->getCorpId();

        if ($ticket = $this->getCache()->fetch($key)) {
            return $ticket;
        }

        $result = $this->parseJSON('get', [self::API_GET_TICKET]);

        $this->getCache()->save($key, $result['ticket'], $result['expired_in'] - 500);

        return $result['ticket'];
    }

    public function getCache() {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    public function getColors() {
        $colors = [
            'Color010' => '#63B359',
            'Color020' => '#2C9F67',
            'Color030' => '#509FC9',
            'Color040' => '#5885CF',
            'Color050' => '#9062C0',
            'Color060' => '#D09A45',
            'Color070' => '#E4B138',
            'Color080' => '#EE903C',
            'Color081' => '#F08500',
            'Color082' => '#A9D92D',
            'Color090' => '#DD6549',
            'Color100' => '#CC463D',
            'Color101' => '#CF3E36',
            'Color102' => '#5E6671',
        ];

        return new Collection($colors);
    }

    public function create($cardType = 'general_coupon', array $baseInfo = [], array $especial = [], array
    $advanceInfo = []) {
        $param = [
            'card' => [
                'card_type' => strtoupper($cardType),
                strtolower($cardType) => array_merge(['base_info' => $baseInfo], $especial, $advanceInfo),
            ],
        ];

        return $this->parseJSON('json', [
            self::API_CREATE_CARD,
            $param,
        ]);
    }

    public function getCard($cardId) {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [
            self::API_GET_CARD,
            $params,
        ]);
    }

    public function lists($offset = 0, $count = 10, $status = 'CARD_STATUS_VERIFY_OK') {
        $params = [
            'offset' => $offset,
            'count' => $count,
            'status' => $status,
        ];

        return $this->parseJSON('json', [
            self::API_GET_CARD_LIST,
            $params,
        ]);
    }

    public function increaseStock($cardId, $amount) {
        return $this->updateStock($cardId, $amount, 'increase');
    }

    public function reduceStock($cardId, $amount) {
        return $this->updateStock($cardId, $amount, 'reduce');
    }

    public function updateStock($cardId, $amount, $action = 'increase') {
        $key = $action === 'increase' ? 'increase_stock_value' : 'reduce_stock_value';

        $params = [
            'card_id' => $cardId,
            $key => abs($amount),
        ];

        return $this->parseJSON('json', [
            self::API_MODIFY_STOCK,
            $params,
        ]);
    }

    public function delete($cardId) {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [
            self::API_DELETE_CARD,
            $params,
        ]);
    }

    public function send($cardId, $agentId = 1, $touser = '@all', $toparty = '', $totag = '') {
        $params = [
            'touser' => $touser,
            'toparty' => $toparty,
            'totag' => $totag,
            'agentid' => $agentId,
            'msgtype' => 'card',
            'card' => [
                'card_id' => $cardId,
            ],
        ];

        return $this->parseJSON('json', [
            self::API_SEND_CARD,
            $params,
        ]);
    }

    public function getHtml($cardId, $agentId = 1) {
        $params = [
            'card_id' => $cardId,
            'agentid' => $agentId,
        ];

        return $this->parseJSON('json', [
            self::API_GET_HTML,
            $params,
        ]);
    }

    public function QRCode(array $card_info = [], $expire_seconds = '') {
        $params = [
            'action_name' => 'QR_CARD',
            'expire_seconds' => $expire_seconds,
            'action_info' => [
                'card' => $card_info,
            ],
        ];

        return $this->parseJSON('json', [
            self::API_CREATE_QRCODE,
            $params,
        ]);
    }

    public function getCode($code) {
        $params = [
            'code' => $code,
        ];

        return $this->parseJSON('json', [
            self::API_GET_CODE,
            $params,
        ]);
    }

    public function consume($code) {
        $params = [
            'code' => $code,
        ];

        return $this->parseJSON('json', [
            self::API_CONSUME_CODE,
            $params,
        ]);
    }
    
    

}