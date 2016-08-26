<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/26
 * Time: 17:16
 */

namespace RCorpWechat\Message;

class Text extends AbstractMessage
{
    protected $type = 'text';

    protected $properties = [
        'content'
    ];
}