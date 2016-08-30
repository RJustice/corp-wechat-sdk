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

class Card extends AbstractAPI
{
    protected $cache;

    const ticket_cache_prefix = 'rcorpwechat.card_api_ticket.';

    const api_create_card = '';
    const api_get_card = '';
    const api_get_card_list = '';
    const api_modify_stock = '';
    const api_delete_card = '';
    const api_send_card = '';
    const api_get_html = '';
    const api_create_qrcode = '';
    const api_get_code = '';
    const api_consume_code = '';
    const api_
}