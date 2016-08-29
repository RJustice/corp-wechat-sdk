<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2016/8/29
 * Time: 10:34
 */

namespace RCorpWechat\Message;

class Link extends AbstractMessage
{
    protected $type = 'link';

    protected $properties = [
        'title',
        'description',
        'url'
    ];

    protected $aliases = [
        'url' => 'picurl',
    ];
}